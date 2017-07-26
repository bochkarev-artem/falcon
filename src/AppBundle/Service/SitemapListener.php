<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\LocalePageInterface;
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
     * @var integer
     */
    protected $bookOffset = 0;

    /**
     * @var integer
     */
    protected $authorOffset = 0;

    /**
     * @var LocaleService
     */
    protected $localeService;

    /**
     * @param EntityManager $em
     * @param LocaleService $localeService
     */
    public function __construct(EntityManager $em, LocaleService $localeService)
    {
        $this->em            = $em;
        $this->localeService = $localeService;
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $queryBuilders = [
            'genres'  => 'Genre',
            'authors' => 'Author',
            'series'  => 'Sequence',
            'tags'    => 'Tag',
            'books'   => 'Book',
            'books2'  => 'Book',
            'books3'  => 'Book',
            'books4'  => 'Book',
            'books5'  => 'Book',
            'books6'  => 'Book',
            'books7'  => 'Book',
            'books8'  => 'Book',
            'books9'  => 'Book',
            'books10' => 'Book',
        ];

        $host = $this->localeService->getHost();
        if (!$host) {
            return;
        }
        foreach ($queryBuilders as $section => $entityName) {
            if (is_null($event->getSection()) || $event->getSection() == $section) {
                $query = $this->getQuery($entityName);
                foreach ($query->iterate() as $row) {
                    /** @var PageInterface|LocalePageInterface $entity */
                    $entity = array_shift($row);
                    if ($entity instanceof PageInterface) {
                        $event
                            ->getUrlContainer()
                            ->addUrl(new UrlConcrete($host . $entity->getPath()), $section);
                    } elseif ($entity instanceof LocalePageInterface) {
                        $path = $this->localeService->getLocaleField($entity, 'path');
                        $event->getUrlContainer()->addUrl(new UrlConcrete($host . $path), $section);
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

        return $qb->getQuery();
    }
}
