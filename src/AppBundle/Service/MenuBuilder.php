<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\ConfigCache;

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
    protected $cacheMainFile;

    /**
     * @var string
     */
    protected $cacheSideFile;

    /**
     * @var string
     */
    protected $cacheMobileFile;

    /**
     * @var QueryService
     */
    protected $queryService;

    /**
     * @param EntityManager     $em
     * @param \Twig_Environment $twig
     * @param string            $cacheDir
     * @param QueryService      $queryService
     */
    public function __construct(EntityManager $em, \Twig_Environment $twig, $cacheDir, QueryService $queryService)
    {
        $this->em              = $em;
        $this->twig            = $twig;
        $this->queryService    = $queryService;
        $cacheDir              = preg_replace('/\/cache\/front\/dev/', '/cache/prod', $cacheDir);
        $this->cacheDir        = $cacheDir . '/menuCache';
        $this->cacheMainFile   = $this->cacheDir . '/mainMenu.html';
        $this->cacheSideFile   = $this->cacheDir . '/sideMenu.html';
        $this->cacheMobileFile = $this->cacheDir . '/mobileMenu.html';
    }

    /**
     * @return string
     */
    public function getMainMenu()
    {
        $cache = new ConfigCache($this->cacheMainFile, false);

        if (!$cache->isFresh()) {
            $this->updateCache($cache, 'main');
        }

        return file_get_contents($cache->getPath());
    }

    /**
     * @return string
     */
    public function getMobileMenu()
    {
        $cache = new ConfigCache($this->cacheMobileFile, false);

        if (!$cache->isFresh()) {
            $this->updateCache($cache, 'mobile');
        }

        return file_get_contents($cache->getPath());
    }

    /**
     * @return string
     */
    public function getSideMenu()
    {
        $cache = new ConfigCache($this->cacheSideFile, false);

        if (!$cache->isFresh()) {
            $this->updateCache($cache, 'side');
        }

        return file_get_contents($cache->getPath());
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
                ->andWhere($qb->expr()->eq('b.featuredMenu', ':featured_menu'))
                ->setParameter('featured_menu', true)
                ->addOrderBy('b.rating', 'DESC')
                ->addOrderBy('b.reviewCount', 'DESC')
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
        $this->em->clear('AppBundle\Entity\Genre');

        $categoryRepo = $this->em->getRepository('AppBundle:Genre');
        $qb           = $categoryRepo->createQueryBuilder('g');
        $qb->addOrderBy('g.title');

        $categories = $qb->getQuery()->getResult() ?: [];

        return $categories;
    }
}
