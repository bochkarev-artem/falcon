<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Model\QueryParams;

class HomePageService
{
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
     * @param array $bookIds
     *
     * @return array
     */
    public function getFeaturedBooks($bookIds)
    {
        $queryParams = new QueryParams();
        $queryParams->setFilterId($bookIds);

        $queryResult = $this->queryService->query($queryParams);
        $books       = $queryResult->getResults();

        return $books;
    }
}
