<?php

namespace AppBundle\Service;

use AppBundle\Entity\Ads;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdsManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $cacheFile;

    /**
     * @var LocaleService
     */
    protected $localeService;

    /**
     * @var User|null
     */
    protected $user = null;

    /**
     * @param EntityManagerInterface $em
     * @param LocaleService          $localeService
     * @param TokenStorageInterface  $tokenStorage
     * @param string                 $cacheDir
     */
    public function __construct(
        EntityManagerInterface $em,
        LocaleService $localeService,
        TokenStorageInterface $tokenStorage,
        $cacheDir
    ) {
        $this->em = $em;
        $cacheDir = preg_replace('/\/cache\/front\/dev/', '/cache/prod', $cacheDir);
        $this->cacheDir = $cacheDir . '/adsCache';
        $this->cacheFile = $this->cacheDir . '/%s_ads.%s.html';
        $this->localeService = $localeService;
        $token = $tokenStorage->getToken();
        if (null !== $token && is_object($user = $token->getUser())) {
            $this->user = $user;
        }
    }

    /**
     * @param integer $position
     *
     * @return Ads|null
     */
    protected function findOneByPosition($position)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('ads')
            ->from('AppBundle:Ads', 'ads')
            ->andWhere($qb->expr()->eq('ads.position', ':position'))
            ->andWhere($qb->expr()->eq('ads.active', ':active'))
            ->andWhere($qb->expr()->eq('ads.lang', ':language'))
            ->setParameter('language', $this->localeService->getLocale())
            ->setParameter('position', $position)
            ->setParameter('active', true)
            ->orderBy('ads.priority', Criteria::ASC)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param integer $position
     * @param boolean $showToAll
     *
     * @return string
     */
    public function getAdByPosition($position, $showToAll)
    {
        if ($showToAll || $this->user) {
            $fileName = $this->getCacheFileName($position);
            $cache = new ConfigCache($fileName, false);

            if (!$cache->isFresh()) {
                $this->updateCache($cache, $position);
            }

            $ads = file_get_contents($cache->getPath());
        } else {
            $ads = '';
        }

        return $ads;
    }

    /**
     * @param integer $position
     *
     * @return string
     */
    protected function getCacheFileName($position)
    {
        $fileName = sprintf($this->cacheFile, $position, $this->localeService->getLocale());

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
     * @param ConfigCache $cache
     * @param integer     $position
     *
     * @throws \RuntimeException
     */
    protected function updateCache(ConfigCache $cache, $position)
    {
        $ad = $this->findOneByPosition($position);
        $content = $ad ? $ad->getCode() : '';

        $this->checkCacheFolder();
        $cache->write($content);
    }

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
        }
    }
}
