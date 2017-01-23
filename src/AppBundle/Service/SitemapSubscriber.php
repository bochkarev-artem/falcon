<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\EntityInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SitemapSubscriber implements EventSubscriberInterface
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
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'generateSitemap',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function generateSitemap(SitemapPopulateEvent $event)
    {
        $queryBuilders[] = $this->getAuthorQueryBuilder();
        $queryBuilders[] = $this->getBookQueryBuilder();
        $queryBuilders[] = $this->getGenreQueryBuilder();
        $queryBuilders[] = $this->getSequenceQueryBuilder();
        $queryBuilders[] = $this->getTagQueryBuilder();

        $sections[] = 'authors';
        $sections[] = 'books';
        $sections[] = 'genres';
        $sections[] = 'series';
        $sections[] = 'tags';

        foreach ($queryBuilders as $queryBuilder) {
            foreach ($queryBuilder->getQuery()->iterate() as $row) {
                /** @var EntityInterface $entity */
                $entity  = array_shift($row);
                $section = array_shift($sections);
                $event->getUrlContainer()->addUrl(new UrlConcrete($this->siteUrl . $entity->getPath()), $section);
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
