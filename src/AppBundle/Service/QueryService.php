<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Model\QueryParams;
use AppBundle\Model\QueryResult;
use Elastica\Query;
use Elastica\Type;

class QueryService
{
    /**
     * @var Type
     */
    private $repository;

    /**
     * @param Type $repository
     */
    public function __construct(Type $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param QueryParams $queryParams
     *
     * @return QueryResult
     */
    public function query(QueryParams $queryParams)
    {
        $filters = new Query\BoolQuery();
        $this->applyFilters($queryParams, $filters);

        $baseQuery = $this->getBaseQuery();

        $filtered = new Query\Filtered($baseQuery, $filters);
        $query    = new Query();
        $query->setQuery($filtered);

        $this->applySorting($query);

        $query->setFrom($queryParams->getStart());
        $query->setSize($queryParams->getSize());

        return $this->getResult($query);
    }

    /**
     * @return Query\AbstractQuery|Query\MatchAll
     */
    private function getBaseQuery()
    {
        $baseQuery = new Query\MatchAll();

        return $baseQuery;
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $filters
     */
    private function applyFilters(QueryParams $queryParams, Query\BoolQuery $filters) {
        if ($queryParams->getFilterId()) {
            $this->applyIdFilter($queryParams, $filters);
        }
    }

    /**
     * @param Query $query
     *
     * @return QueryResult
     */
    private function getResult(Query $query)
    {
        $options = [];

        $result = new QueryResult($this->repository->search($query, $options));

        return $result;
    }

    /**
     * @param Query $query
     */
    private function applySorting(Query $query)
    {
        $query->addSort(['book_id' => 'asc']);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $filters
     */
    private function applyIdFilter(QueryParams $queryParams, Query\BoolQuery $filters)
    {
        $filterId = $queryParams->getFilterId();
        $filter   = is_array($filterId) ?
            new Query\Terms('book_id', $filterId) :
            new Query\Term(['book_id' => $filterId]);

        $filters->addMust($filter);
    }

    /**
     * @param array $bookData
     *
     * @return array
     */
    public function buildBooksData($bookData)
    {
        $result = [];
        foreach ($bookData as $book) {
            $bookData        = $book->getData();
            $bookId          = $bookData['book_id'];
            $result[$bookId] = $bookData;
        }

        return $result;
    }
}

