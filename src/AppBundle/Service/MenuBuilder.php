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
     * @var string
     */
    protected $locale;

    /**
     * @param EntityManager     $em
     * @param \Twig_Environment $twig
     * @param string            $cacheDir
     * @param QueryService      $queryService
     * @param LocaleService     $localeService
     */
    public function __construct(
        EntityManager $em,
        \Twig_Environment $twig,
        $cacheDir,
        QueryService $queryService,
        LocaleService $localeService
    ) {
        $this->em            = $em;
        $this->twig          = $twig;
        $this->queryService  = $queryService;
        $cacheDir            = preg_replace('/\/cache\/front\/dev/', '/cache/prod', $cacheDir);
        $this->cacheDir      = $cacheDir . '/menuCache';
        $this->cacheFile     = $this->cacheDir . '/%sMenu.%s.html';
        $this->localeService = $localeService;
        $this->locale        = $localeService->getLocale();
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
        $fileName = sprintf($this->cacheFile, $type, $this->locale);

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
                ->setParameter('locale', $this->locale)
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
        $qb->addOrderBy('g.title' . ucfirst($this->locale));

        $categories = $qb->getQuery()->getResult() ?: [];

        return $categories;
    }
}
