<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

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
     * @param EntityManager     $em
     * @param \Twig_Environment $twig
     * @param string            $cacheDir
     */
    public function __construct(EntityManager $em, \Twig_Environment $twig, $cacheDir)
    {
        $this->em              = $em;
        $this->twig            = $twig;
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

        return $this->twig->render(
            'AppBundle:Elements:Header/' . $type. '_menu.html.twig',
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
