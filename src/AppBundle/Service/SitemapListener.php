<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\PageInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
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
     * @var int
     */
    protected $bookOffset = 0;

    /**
     * @var int
     */
    protected $authorOffset = 0;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $queryBuilders = [
            'authors'  => 'Author',
            'authors2' => 'Author',
            'authors3' => 'Author',
            'authors4' => 'Author',
            'series'   => 'Sequence',
            'tags'     => 'Tag',
            'genres'   => 'Genre',
            'books'    => 'Book',
            'books2'   => 'Book',
            'books3'   => 'Book',
            'books4'   => 'Book',
            'books5'   => 'Book',
        ];

        $host = 'http://bookary.ru/';
        foreach ($queryBuilders as $section => $entityName) {
            $query = $this->getQuery($entityName);
            foreach ($query->iterate() as $row) {
                /** @var PageInterface $entity */
                $entity = array_shift($row);
                if ($entity instanceof PageInterface) {
                    $event
                        ->getUrlContainer()
                        ->addUrl(new UrlConcrete($host . $entity->getPath()), $section);
                }
            }
            $query = null;
            $this->em->clear();
        }
    }

    /**
     * @param string $entityName
     *
     * @return Query
     */
    protected function getQuery($entityName)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e')
            ->from("AppBundle:$entityName", 'e')
        ;

        if ('Genre' == $entityName) {
            $qb->andWhere($qb->expr()->isNotNull('e.parent'));
        }

        if ('Book' == $entityName) {
            $qb->setMaxResults(30000);

            if ($this->bookOffset) {
                $qb->setFirstResult($this->bookOffset + 1);
            }

            $this->bookOffset += 30000;
        }

        if ('Author' == $entityName) {
            $qb->setMaxResults(30000);

            if ($this->authorOffset) {
                $qb->setFirstResult($this->authorOffset + 1);
            }

            $this->authorOffset += 30000;
        }

        return $qb->getQuery();
    }
}
