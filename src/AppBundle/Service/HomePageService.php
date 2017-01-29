<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Model\QueryParams;

class HomePageService
{
    const FEATURED_HOME_COUNT = 9;

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
     * @return array
     */
    public function getFeaturedBooks()
    {
        $queryParams = new QueryParams();
        $queryParams
            ->setFilterFeaturedHome()
            ->setSize(self::FEATURED_HOME_COUNT)
        ;

        $queryResult = $this->queryService->query($queryParams);
        $books       = $queryResult->getResults();

        return $books;
    }
}
