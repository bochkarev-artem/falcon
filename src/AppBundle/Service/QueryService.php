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

        $baseQuery = $this->getBaseQuery($queryParams);
        $filtered  = new Query\Filtered($baseQuery, $filters);
        $query     = new Query();

        $query->setQuery($filtered);

        $this->applySorting($query, $queryParams);

        $query->setFrom($queryParams->getStart());
        $query->setSize($queryParams->getSize());

        return $this->getResult($query);
    }

    /**
     * @param QueryParams $queryParams
     *
     * @return Query\AbstractQuery|Query\MatchAll
     */
    private function getBaseQuery($queryParams)
    {
        if ($queryParams->getSearchQuery()) {
            $baseQuery = $this->getSearchQuery($queryParams);
        } else {
            $baseQuery = new Query\MatchAll();
        }

        return $baseQuery;
    }

    /**
     * @param QueryParams $queryParams
     *
     * @return Query\AbstractQuery
     */
    private function getSearchQuery(QueryParams $queryParams)
    {
        if ($queryString = $queryParams->getSearchQuery()) {
            $fields = [
                'book_title.exact^3',
                'author_name.exact^6',
                'sequence_title.exact',
                'tag_title.exact^2',
                'genre_title.exact',
            ];

            $query = new Query\MultiMatch();
            $query
                ->setQuery($queryString)
                ->setFields($fields)
                ->setTieBreaker(0.3)
                ->setOperator('and')
                ->setParam('fuzziness', '1')
                ->setParam('lenient', true)
            ;
        } else {
            $query = new Query\Match();
            $query->setField('_id', '-1');
        }

        return $query;
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $filters
     */
    private function applyFilters(QueryParams $queryParams, Query\BoolQuery $filters) {
        if ($queryParams->getFilterId()) {
            $this->applyIdFilter($queryParams, $filters);
        }

        if ($queryParams->getFilterGenres()) {
            $this->applyGenreFilter($queryParams, $filters);
        }

        if ($queryParams->getFilterAuthors()) {
            $this->applyAuthorFilter($queryParams, $filters);
        }

        if ($queryParams->getFilterTags()) {
            $this->applyTagFilter($queryParams, $filters);
        }

        if ($queryParams->getFilterSequences()) {
            $this->applySequenceFilter($queryParams, $filters);
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
     * @param Query       $query
     * @param QueryParams $queryParams
     */
    private function applySorting(Query $query, QueryParams $queryParams)
    {
        if ($queryParams->getSearchQuery()) {
            $query->addSort(['_score' => 'desc', 'book_id' => 'desc']);
        } else {
            $query->addSort(['book_id' => 'desc']);
        }
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $query
     */
    private function applyIdFilter(QueryParams $queryParams, Query\BoolQuery $query)
    {
        $queryId   = $queryParams->getFilterId();
        $queryTerm = is_array($queryId) ?
            new Query\Terms('book_id', $queryId) :
            new Query\Term(['book_id' => $queryId]);

        $query->addMust($queryTerm);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $filters
     */
    private function applyGenreFilter(QueryParams $queryParams, Query\BoolQuery $filters)
    {
        $genreIds    = $queryParams->getFilterGenres();
        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('genres');

        $queryTerm = is_array($genreIds) ?
            new Query\Terms('genres.genre_id', $genreIds) :
            new Query\Term(['genres.genre_id' => $genreIds])
        ;

        $nestedQuery->setQuery($queryTerm);
        $filters->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $filters
     */
    private function applyAuthorFilter(QueryParams $queryParams, Query\BoolQuery $filters)
    {
        $authorIds   = $queryParams->getFilterAuthors();
        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('authors');

        $queryTerm = is_array($authorIds) ?
            new Query\Terms('authors.author_id', $authorIds) :
            new Query\Term(['authors.author_id' => $authorIds])
        ;

        $nestedQuery->setQuery($queryTerm);
        $filters->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $filters
     */
    private function applyTagFilter(QueryParams $queryParams, Query\BoolQuery $filters)
    {
        $tagIds      = $queryParams->getFilterTags();
        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('tags');

        $queryTerm = is_array($tagIds) ?
            new Query\Terms('tags.tag_id', $tagIds) :
            new Query\Term(['tags.tag_id' => $tagIds])
        ;

        $nestedQuery->setQuery($queryTerm);
        $filters->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $filters
     */
    private function applySequenceFilter(QueryParams $queryParams, Query\BoolQuery $filters)
    {
        $sequenceIds    = $queryParams->getFilterSequences();
        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('sequence');

        $queryTerm = is_array($sequenceIds) ?
            new Query\Terms('sequence.sequence_id', $sequenceIds) :
            new Query\Term(['sequence.sequence_id' => $sequenceIds])
        ;

        $nestedQuery->setQuery($queryTerm);
        $filters->addMust($nestedQuery);
    }
}
