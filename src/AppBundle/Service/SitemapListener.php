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
            'ru/authors'  => 'Author',
            'ru/authors2' => 'Author',
            'ru/authors3' => 'Author',
            'ru/series'   => 'Sequence',
            'ru/tags'     => 'Tag',
            'ru/genres'   => 'Genre',
            'ru/books'    => 'Book',
            'ru/books2'   => 'Book',
            'ru/books3'   => 'Book',
            'ru/books4'   => 'Book',
            'ru/books5'   => 'Book',
            'ru/books6'   => 'Book',
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
