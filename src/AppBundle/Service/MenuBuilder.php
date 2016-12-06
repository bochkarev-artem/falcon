<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MenuBuilder
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Twig_Environment
     */
    protected $templating;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $cacheFileName;

    /**
     * @param EntityManager      $em
     * @param ContainerInterface $container
     * @param string             $cacheDir
     */
    public function __construct(
        EntityManager      $em,
        ContainerInterface $container,
        $cacheDir
    ) {
        $this->em            = $em;
        $this->container     = $container;
        $this->cacheDir      = $cacheDir . '/mainMenuCache';
        $this->cacheFileName = $this->cacheDir . '/mainMenu.html';
    }

    /**
     * @return string
     */
    public function getMainMenu()
    {
        $cache = new ConfigCache($this->cacheFileName, false);

        if (!$cache->isFresh()) {
            $this->updateCache($cache);
        }

        return file_get_contents($cache->getPath());
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTemplating()
    {
        if (!isset($this->templating)) {
            $this->templating = $this->container->get('twig');
        }

        return $this->templating;
    }

    /**
     * Creates cache folder if it doesn't exist
     */
    protected function checkCacheFolder()
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * @return string
     */
    protected function buildMainMenu()
    {
        $categories             = $this->getAllGenres();
//        $productCategoryTree    = $this->buildTreeStructure($productCategories);

        return $this->getTemplating()->render(
            'AppBundle:Elements:Header/main-menu.html.twig',
            array(
//                'product_tree_categories' => $productCategoryTree,
//                'menu_genres'             => $this->getMainMenuGenres(array_keys($productCategoryTree)),
//                'menu_products'           => $this->getMainMenuProducts(),
            )
        );
    }

    /**
     * @return array
     */
    protected function getMainMenuProducts()
    {
        $products = [];
        foreach($products as $product) {

            foreach ($product['featured'] as $featured) {
                if (
                    ProductFeatured::TYPE_TOP_MENU == $featured['type']
                    && ProductFeatured::SCOPE_CATEGORY == $featured['scope']
                    && isset($featured['scope_id'])
                ) {
                    $rootCategoryId            = $featured['scope_id'];
                    $products[$rootCategoryId] = $product;
                }
            }
        }

        return $products;
    }

    /**
     * @param $topLevelCategoryIds
     *
     * @return array
     */
    protected function getMainMenuGenres($topLevelCategoryIds)
    {
        $productQueryParams = new ProductQueryParams();
        $productQueryParams->setAggregateBrands();

        $categoryBrands = [];
        foreach ($topLevelCategoryIds as $topLevelCategoryId) {
            $productQueryParams->setFilterCategories($topLevelCategoryId);
            $brands = $this->productQueryService
                ->query($productQueryParams)
                ->setLocale($this->requestStack->getCurrentRequest()->getLocale())
                ->getFilters();

            $categoryBrands[$topLevelCategoryId] = isset($brands['brands']['terms']) ? $brands['brands']['terms'] : [];
            usort($categoryBrands[$topLevelCategoryId], function ($brand1, $brand2) {
                return strcmp($brand1['term_label'], $brand2['term_label']);
            });
        }

        return $categoryBrands;
    }

    /**
     * @param ConfigCache $cache
     *
     * @throws \RuntimeException
     */
    protected function updateCache(ConfigCache $cache)
    {
        $content = $this->buildMainMenu();
        $this->checkCacheFolder();
        $cache->write($content);
        $this->resetSystemCaches();
    }

    /**
     * @param string|null $cacheFilename
     */
    protected function resetSystemCaches($cacheFilename = null)
    {
        if (is_null($cacheFilename)) {
            $this->cacheFileName;
        }

        if (ini_get('apc.enabled')) {
            if (apc_exists($cacheFilename) && !apc_delete_file($cacheFilename)) {
                throw new \RuntimeException(sprintf('Failed to clear APC Cache for file %s', $cacheFilename));
            }
        }
        elseif (ini_get('opcache.enable')) {
            if (!opcache_invalidate($cacheFilename, true)) {
                throw new \RuntimeException(sprintf('Failed to clear OPCache for file %s', $cacheFilename));
            }
        }
    }

    /**
     * Resets menu cache
     */
    public function resetCache()
    {
        // ensure that elastica index is updated before resetting cache
        sleep(1);

        //refresh ES index to ensure that just indexed products are searchable
//        $this->container->get('fos_elastica.index.products')->refresh();

        $this->checkCacheFolder();

        $finder = new Finder();
        $finder->files()->in($this->cacheDir);

        /* @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fullPath = $file->getRealPath();
            if (\file_exists($fullPath)) {
                unlink($fullPath);
            }
            $this->resetSystemCaches($fullPath);
        }
    }

    /**
     * @return array
     */
    protected function getAllGenres()
    {
        $this->em->clear('AppBundle\Entity\Category');

        $categoryRepo = $this->em->getRepository('AppBundle:Genre');
        $qb           = $categoryRepo->createQueryBuilder('g');
        $qb->addOrderBy('g.title');

        $categories = $qb->getQuery()->getResult() ?: [];

        return $categories;
    }

    /**
     * @param array $categories
     *
     * @return array
     */
    protected function getCategoryIds($categories)
    {
        $categoryCount = count($categories);
        $elasticIds    = [];
        if ($categoryCount > 0) {
            $params = new ProductQueryParams();
            $params
                ->setAggregateCategoryIds()
                ->setAggregateCategoryIdsSize($categoryCount)
                ->setReturnProducts(0)
            ;
            $categoryIds = $this->productQueryService->query($params)->getAggregation('category_ids');
            $elasticIds  = array_map(function (array $category) {
                return $category['key'];
            }, $categoryIds['buckets']);
        }

        return $elasticIds;
    }
}
