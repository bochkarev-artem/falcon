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
     * @var string
     */
    protected $siteUrl;

    /**
     * @var integer
     */
    protected $bookOffset = 0;

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
            'books'   => 'Book',
            'books2'  => 'Book',
            'authors' => 'Author',
            'genres'  => 'Genre',
            'series'  => 'Sequence',
            'tags'    => 'Tag',
        ];

        foreach ($queryBuilders as $section => $entityName) {
            if (is_null($event->getSection()) || $event->getSection() == $section) {
                $query = $this->getQuery($entityName);
                foreach ($query->iterate() as $row) {
                    /** @var PageInterface $entity */
                    $entity = array_shift($row);
                    if ($entity instanceof PageInterface) {
                        $event->getUrlContainer()->addUrl(new UrlConcrete($this->siteUrl . $entity->getPath()), $section);
                    }
                }
                $query = null;
                $this->em->clear();
            }
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

        if ('Book' == $entityName) {
            $qb->setMaxResults(50000);

            if ($this->bookOffset) {
                $qb->setFirstResult($this->bookOffset + 1);
            }

            $this->bookOffset += 50000;
        }

        return $qb->getQuery();
    }
}
