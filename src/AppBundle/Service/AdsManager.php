<?php

namespace AppBundle\Service;

use AppBundle\Entity\Ads;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\ConfigCache;

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
     * @param EntityManagerInterface $em
     * @param LocaleService          $localeService
     * @param string                 $cacheDir
     */
    public function __construct(EntityManagerInterface $em, LocaleService $localeService, $cacheDir)
    {
        $this->em = $em;
        $cacheDir = preg_replace('/\/cache\/front\/dev/', '/cache/prod', $cacheDir);
        $this->cacheDir = $cacheDir . '/adsCache';
        $this->cacheFile = $this->cacheDir . '/%s_ads.%s.html';
        $this->localeService = $localeService;
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
            ->setParameter('position', $position)
            ->setParameter('active', true)
            ->orderBy('ads.priority', Criteria::ASC)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param integer $position
     *
     * @return string
     */
    public function getAdByPosition($position)
    {
        $fileName = $this->getCacheFileName($position);
        $cache = new ConfigCache($fileName, false);

        if (!$cache->isFresh()) {
            $this->updateCache($cache, $position);
        }

        return file_get_contents($cache->getPath());
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
}
