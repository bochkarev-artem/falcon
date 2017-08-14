<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use Doctrine\ORM\EntityManager;
use Elastica\Index;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MenuBuilder
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $cacheFile;

    /**
     * @var QueryService
     */
    protected $queryService;

    /**
     * @var LocaleService
     */
    protected $localeService;

    /**
     * @var Index
     */
    protected $bookIndex;

    /**
     * @param EntityManager     $em
     * @param \Twig_Environment $twig
     * @param string            $cacheDir
     * @param QueryService      $queryService
     * @param LocaleService     $localeService
     * @param Index             $bookIndex
     */
    public function __construct(
        EntityManager $em,
        \Twig_Environment $twig,
        $cacheDir,
        QueryService $queryService,
        LocaleService $localeService,
        Index $bookIndex
    ) {
        $this->em            = $em;
        $this->twig          = $twig;
        $this->queryService  = $queryService;
        $this->cacheDir      = $cacheDir . '/menuCache';
        $this->cacheFile     = $this->cacheDir . '/%sMenu.%s.html';
        $this->localeService = $localeService;
        $this->bookIndex     = $bookIndex;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getMenu($type)
    {
        $fileName = $this->getCacheFileName($type);
        $cache = new ConfigCache($fileName, false);

        if (!$cache->isFresh()) {
            $this->updateCache($cache, $type);
        }

        return file_get_contents($cache->getPath());
    }

    /**
     * @param $type
     *
     * @return string
     */
    protected function getCacheFileName($type)
    {
        $fileName = sprintf($this->cacheFile, $type, $this->localeService->getLocale());

        return $fileName;
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
     * @param array  $menuTree
     * @param string $type
     *
     * @return string
     */
    protected function buildMenu(array $menuTree, $type)
    {
        $parentGenres = $menuTree[0];
        unset($menuTree[0]);

        $data = [
            'parentGenres' => $parentGenres,
            'genres'       => $menuTree,
        ];

        $additionalData = [];
        if ($type == 'main') {
            $menuGenreBooks = [];
            $qb = $this->em->createQueryBuilder();
            $qb
                ->select('b, g')
                ->from('AppBundle:Book', 'b')
                ->leftJoin('b.genres', 'g')
                ->leftJoin('b.ratings', 'rating')
                ->andWhere($qb->expr()->eq('b.featuredMenu', ':featured_menu'))
                ->andWhere($qb->expr()->eq('b.lang', ':locale'))
                ->addOrderBy('rating.rating', 'DESC')
                ->setParameter('featured_menu', true)
                ->setParameter('locale', $this->localeService->getLocale())
            ;

            $books = $qb->getQuery()->getResult();
            /** @var Book $book */
            foreach ($books as $book) {
                foreach ($book->getGenres() as $genre) {
                    $genreId = $genre->getParent()->getId();
                    if (!array_key_exists($genreId, $menuGenreBooks)) {
                        $menuGenreBooks[$genreId] = $book;
                        break;
                    }
                }
            }

            $additionalData['menu_genre_books'] = $menuGenreBooks;
        }

        return $this->twig->render(
            'AppBundle:Elements:Header/' . $type. '_menu.html.twig',
            array_merge($data, $additionalData)
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
            if (!empty($genre->getChildren())) {
                $menuTree[$parentId][] = $genre;
            }
        }

        return $menuTree;
    }

    /**
     * @param ConfigCache $cache
     * @param string      $type
     *
     * @throws \RuntimeException
     */
    protected function updateCache(ConfigCache $cache, $type)
    {
        $genres   = $this->getAllGenres();
        $menuTree = $this->buildMenuTree($genres);
        $content  = $this->buildMenu($menuTree, $type);

        $this->checkCacheFolder();
        $cache->write($content);
    }

    /**
     * @param $file
     *
     * @throws \RuntimeException
     */
    protected function throwException($file)
    {
        throw new \RuntimeException(sprintf('Failed to clear APC Cache for file %s', $file));
    }

    /**
     * @return array
     */
    protected function getAllGenres()
    {
        $locale = $this->localeService->getLocale();
        $this->em->clear('AppBundle\Entity\Genre');
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('g')
            ->from("AppBundle:Genre", 'g', 'g.id')
            ->leftJoin('g.books', 'b')
            ->andWhere($qb->expr()->eq('b.lang', ':locale'))
            ->setParameter('locale', $locale)
            ->groupBy('g.id')
            ->having($qb->expr()->gt('COUNT(b)', 0))
            ->addOrderBy('g.title' . ucfirst($locale));

        $categories = $qb->getQuery()->getResult() ?: [];

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('g')
            ->from("AppBundle:Genre", 'g')
            ->where($qb->expr()->isNull('g.parent'))
            ->addOrderBy('g.title' . ucfirst($locale));

        $parentCategories = $qb->getQuery()->getResult() ?: [];

        foreach ($parentCategories as $parentCategory) {
            $categories[] = $parentCategory;
        }

        return $categories;
    }

    /**
     * Resets menu cache
     */
    public function resetCache()
    {
        // ensure that elastica index is updated before resetting cache
        sleep(1);
        $this->bookIndex->refresh();

        $this->checkCacheFolder();

        $finder = new Finder();
        $finder->files()->in($this->cacheDir);

        /* @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fullPath = $file->getRealPath();
            if (\file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}
