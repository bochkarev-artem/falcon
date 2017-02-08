<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\PageInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapListener implements SitemapListenerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $siteUrl;

    /**
     * @param EntityManager $em
     * @param string        $baseUrl
     * @param string        $baseScheme
     */
    public function __construct(EntityManager $em, $baseUrl, $baseScheme)
    {
        $this->em      = $em;
        $this->siteUrl = $baseScheme . '://' . $baseUrl . '/';
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $queryBuilders = [
            'authors' => $this->getAuthorQueryBuilder(),
            'books'   => $this->getBookQueryBuilder(),
            'genres'  => $this->getGenreQueryBuilder(),
            'series'  => $this->getSequenceQueryBuilder(),
            'tags'    => $this->getTagQueryBuilder()
        ];

        foreach ($queryBuilders as $section => $queryBuilder) {
            if (is_null($event->getSection()) || $event->getSection() == $section) {
                foreach ($queryBuilder->getQuery()->iterate() as $row) {
                    /** @var PageInterface $entity */
                    $entity = array_shift($row);
                    if ($entity instanceof PageInterface) {
                        $event->getUrlContainer()->addUrl(new UrlConcrete($this->siteUrl . $entity->getPath()), $section);
                    }
                }
            }
        }
    }

    /**
     * @return QueryBuilder
     */
    protected function getBookQueryBuilder()
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
        ;

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    protected function getGenreQueryBuilder()
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('g')
            ->from('AppBundle:Genre', 'g')
        ;

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    protected function getTagQueryBuilder()
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('t')
            ->from('AppBundle:Tag', 't')
        ;

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    protected function getAuthorQueryBuilder()
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('a')
            ->from('AppBundle:Author', 'a')
        ;

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    protected function getSequenceQueryBuilder()
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('s')
            ->from('AppBundle:Sequence', 's')
        ;

        return $qb;
    }
}
