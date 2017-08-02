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
            'authors' => 'Author',
            'series'  => 'Sequence',
            'genres'  => 'Genre',
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

        $locale = $event->getSection();
        if ('ru' == $locale) {
            $queryBuilders['tags'] = 'Tag';
        }
        $host = 'http://' . $this->localeService->getHosts()[$locale] . '/';
        foreach ($queryBuilders as $section => $entityName) {
            $query = $this->getQuery($entityName, $locale);
            foreach ($query->iterate() as $row) {
                /** @var PageInterface|LocalePageInterface $entity */
                $entity = array_shift($row);
                if ($entity instanceof PageInterface) {
                    $event
                        ->getUrlContainer()
                        ->addUrl(new UrlConcrete($host . $entity->getPath()), $locale . '/' . $section);
                } elseif ($entity instanceof LocalePageInterface) {
                    $path = $this->localeService->getLocaleField($entity, 'path');
                    $event->getUrlContainer()->addUrl(new UrlConcrete($host . $path), $locale . '/' . $section);
                }
            }
            $query = null;
            $this->em->clear();
        }
    }

    /**
     * @param string $entityName
     * @param string $locale
     *
     * @return Query
     */
    protected function getQuery($entityName, $locale)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e')
            ->from("AppBundle:$entityName", 'e')
        ;

        if ('Genre' == $entityName) {
            $qb->andWhere($qb->expr()->isNotNull('e.parent'));
        }

        if ('Book' == $entityName || 'Author' == $entityName || 'Sequence' == $entityName) {
            $qb
                ->andWhere($qb->expr()->eq('e.lang', ':lang'))
                ->setParameter('lang', $locale)
            ;
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
