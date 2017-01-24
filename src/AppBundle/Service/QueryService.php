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
        $query     = new Query();
        $boolQuery = new Query\BoolQuery();
        $this->applyFilters($queryParams, $boolQuery);

        $baseQuery = $this->getBaseQuery($queryParams);
        $boolQuery->addFilter($baseQuery);
        $query->setQuery($boolQuery);

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
     * @param Query\BoolQuery $boolQuery
     */
    private function applyFilters(QueryParams $queryParams, Query\BoolQuery $boolQuery) {
        if ($queryParams->getFilterId()) {
            $this->applyIdFilter($queryParams, $boolQuery);
        }

        if ($queryParams->getFilterGenres()) {
            $this->applyGenreFilter($queryParams, $boolQuery);
        }

        if ($queryParams->getFilterAuthors()) {
            $this->applyAuthorFilter($queryParams, $boolQuery);
        }

        if ($queryParams->getFilterTags()) {
            $this->applyTagFilter($queryParams, $boolQuery);
        }

        if ($queryParams->getFilterSequences()) {
            $this->applySequenceFilter($queryParams, $boolQuery);
        }
    }

    /**
     * @param Query $query
     *
     * @return QueryResult
     */
    private function getResult(Query $query)
    {
        $result = new QueryResult($this->repository->search($query));

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
     * @param Query\BoolQuery $boolQuery
     */
    private function applyGenreFilter(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        $genreIds    = $queryParams->getFilterGenres();
        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('genres');

        $queryTerm = is_array($genreIds) ?
            new Query\Terms('genres.genre_id', $genreIds) :
            new Query\Term(['genres.genre_id' => $genreIds])
        ;

        $nestedQuery->setQuery($queryTerm);
        $boolQuery->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $boolQuery
     */
    private function applyAuthorFilter(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        $authorIds   = $queryParams->getFilterAuthors();
        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('authors');

        $queryTerm = is_array($authorIds) ?
            new Query\Terms('authors.author_id', $authorIds) :
            new Query\Term(['authors.author_id' => $authorIds])
        ;

        $nestedQuery->setQuery($queryTerm);
        $boolQuery->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $boolQuery
     */
    private function applyTagFilter(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        $tagIds      = $queryParams->getFilterTags();
        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('tags');

        $queryTerm = is_array($tagIds) ?
            new Query\Terms('tags.tag_id', $tagIds) :
            new Query\Term(['tags.tag_id' => $tagIds])
        ;

        $nestedQuery->setQuery($queryTerm);
        $boolQuery->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $boolQuery
     */
    private function applySequenceFilter(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        $sequenceIds    = $queryParams->getFilterSequences();
        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('sequence');

        $queryTerm = is_array($sequenceIds) ?
            new Query\Terms('sequence.sequence_id', $sequenceIds) :
            new Query\Term(['sequence.sequence_id' => $sequenceIds])
        ;

        $nestedQuery->setQuery($queryTerm);
        $boolQuery->addMust($nestedQuery);
    }
}
