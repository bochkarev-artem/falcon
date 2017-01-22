<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\Genre;
use Doctrine\ORM\EntityManager;
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
    protected $cacheMainFile;

    /**
     * @var string
     */
    protected $cacheSideFile;

    /**
     * @param EntityManager     $em
     * @param \Twig_Environment $twig
     * @param string            $cacheDir
     */
    public function __construct(EntityManager $em, \Twig_Environment $twig, $cacheDir)
    {
        $this->em            = $em;
        $this->twig          = $twig;
        $cacheDir            = preg_replace('/\/cache\/front\/dev/', '/cache/prod', $cacheDir);
        $this->cacheDir      = $cacheDir . '/menuCache';
        $this->cacheMainFile = $this->cacheDir . '/mainMenu.html';
        $this->cacheSideFile = $this->cacheDir . '/sideMenu.html';
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
     * @param array $menuTree
     *
     * @return string
     */
    protected function buildMainMenu(array $menuTree)
    {
        $parentGenres = $menuTree[0];
        unset($menuTree[0]);

        return $this->twig->render(
            'AppBundle:Elements:Header/main-menu.html.twig',
            [
                'parentGenres' => $parentGenres,
                'genres'       => $menuTree,
            ]
        );
    }

    /**
     * @param array $menuTree
     *
     * @return string
     */
    protected function buildSideMenu($menuTree)
    {
        $parentGenres = $menuTree[0];
        unset($menuTree[0]);

        return $this->twig->render(
            'AppBundle:Elements:Header/side-menu.html.twig',
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

        switch ($type) {
            case 'side':
                $content = $this->buildSideMenu($menuTree);
                break;
            case 'main':
                $content = $this->buildMainMenu($menuTree);
                break;
            default:
                $content = '';
        }

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
            $cacheFilename = $this->cacheMainFile;
        }

        if (ini_get('apc.enabled')) {
            if (apc_exists($cacheFilename) && !apc_delete_file($cacheFilename)) {
                throw new \RuntimeException(sprintf('Failed to clear APC Cache for file %s', $cacheFilename));
            }
        } elseif (ini_get('opcache.enable')) {
            if (!opcache_invalidate($cacheFilename, true)) {
                throw new \RuntimeException(sprintf('Failed to clear OPCache for file %s', $cacheFilename));
            }
        }

        if (is_null($cacheFilename)) {
            $cacheFilename = $this->cacheSideFile;
        }

        if (ini_get('apc.enabled')) {
            if (apc_exists($cacheFilename) && !apc_delete_file($cacheFilename)) {
                throw new \RuntimeException(sprintf('Failed to clear APC Cache for file %s', $cacheFilename));
            }
        } elseif (ini_get('opcache.enable')) {
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
