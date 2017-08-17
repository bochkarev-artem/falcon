<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Model\QueryParams;
use Pagerfanta\Pagerfanta;

class HomePageService
{
    const FEATURED_HOME_COUNT     = 9;
    const NEW_ARRIVALS_HOME_COUNT = 20;
    const POPULAR_HOME_COUNT      = 20;

    /**
     * @var QueryService
     */
    protected $queryService;

    /**
     * @param QueryService $queryService
     */
    public function __construct(QueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * @return Pagerfanta
     */
    public function getFeaturedBooks()
    {
        $queryParams = new QueryParams();
        $queryParams
            ->setFilterFeaturedHome()
            ->setSize(self::FEATURED_HOME_COUNT)
            ->setHasCover(true)
        ;

        $books = $this->queryService->find($queryParams);

        return $books;
    }

    /**
     * @return Pagerfanta
     */
    public function getNewArrivalsBooks()
    {
        $queryParams = new QueryParams();
        $queryParams
            ->setSort(QueryParams::SORT_DATE_DESC)
            ->setSize(self::NEW_ARRIVALS_HOME_COUNT)
            ->setHasCover(true)
        ;

        $books = $this->queryService->find($queryParams);

        return $books;
    }

    /**
     * @return Pagerfanta
     */
    public function getPopularBooks()
    {
        $queryParams = new QueryParams();
        $queryParams
            ->setSort(QueryParams::SORT_RATING_DESC)
            ->setSize(self::POPULAR_HOME_COUNT)
            ->setHasCover(true)
        ;

        $books = $this->queryService->find($queryParams);

        return $books;
    }
}
