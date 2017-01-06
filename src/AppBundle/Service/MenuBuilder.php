<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\Genre;
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
        $cacheDir            = preg_replace('/\/cache\/front\/dev/', '/cache/prod', $cacheDir);
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
        $genres   = $this->getAllGenres();
        $menuTree = $this->buildMenuTree($genres);

        $parentGenres = $menuTree[0];
        unset($menuTree[0]);

        return $this->getTemplating()->render(
            'AppBundle:Elements:Header/main-menu.html.twig',
            [
                'parentGenres' => $parentGenres,
                'genres'       => $menuTree,
            ]
        );
    }

    /**
     * @param Genre[] $genres
     *
     * @return Genre[]
     */
    protected function buildMenuTree($genres)
    {
        $menuTree = [];
        foreach ($genres as $genre) {
            $parent   = $genre->getParent();
            $parentId = $parent ? $parent->getId() : 0;
            $menuTree[$parentId][] = $genre;
        }

        return $menuTree;
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
        $this->em->clear('AppBundle\Entity\Genre');

        $categoryRepo = $this->em->getRepository('AppBundle:Genre');
        $qb           = $categoryRepo->createQueryBuilder('g');
        $qb->addOrderBy('g.title');

        $categories = $qb->getQuery()->getResult() ?: [];

        return $categories;
    }
}
