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
        $searchQuery = $queryParams->getSearchQuery();
        if ($searchQuery) {
            $baseQuery = $this->getSearchQuery($queryParams);
        } elseif ('' === $searchQuery) {
            $baseQuery = new Query\Match();
            $baseQuery->setField('_id', '-1');
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
        $queryString = $queryParams->getSearchQuery();

        $fields = [
            'book_title.exact^3',
            'author_name.exact^6',
            'sequence_title.exact',
            'tag_title.exact^2',
            'genre_title.exact',
        ];

        $query = new Query\MultiMatch();

        return $query
                ->setQuery($queryString)
                ->setFields($fields)
                ->setTieBreaker(0.3)
                ->setOperator('and')
                ->setParam('fuzziness', '1')
                ->setParam('lenient', true)
        ;
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $boolQuery
     */
    private function applyFilters(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        if ($queryParams->getFilterId()) {
            $this->applyIdFilter($queryParams, $boolQuery);
        }

        if ($queryParams->getFilterExcludeBooks()) {
            $this->applyExcludeBooksFilter($queryParams, $boolQuery);
        }

        if ($queryParams->getFilterExcludeAuthors()) {
            $this->applyExcludeAuthorsFilter($queryParams, $boolQuery);
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

        if ($queryParams->isFilterFeaturedHome()) {
            $this->applyFeaturedHomeFilter($boolQuery);
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
        } elseif ($queryParams->getSort() == QueryParams::SORT_DATE_DESC) {
            $query->addSort(['date' => 'desc', 'book_id' => 'desc']);
        } elseif ($queryParams->getSort() == QueryParams::SORT_RATING_DESC) {
            $query->addSort(['rating' => 'desc', 'review_count' => 'desc']);
        } else {
            $query->addSort(['book_id' => 'desc']);
        }
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $query
     */
    private function applyExcludeBooksFilter(QueryParams $queryParams, Query\BoolQuery $query)
    {
        $bookId    = $queryParams->getFilterExcludeBooks();
        $queryTerm = is_array($bookId) ?
            new Query\Terms('book_id', $bookId) :
            new Query\Term(['book_id' => $bookId])
        ;

        $query->addMustNot($queryTerm);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $query
     */
    private function applyExcludeAuthorsFilter(QueryParams $queryParams, Query\BoolQuery $query)
    {
        $authorIds = $queryParams->getFilterExcludeAuthors();
        $queryTerm = is_array($authorIds) ?
            new Query\Terms('authors.author_id', $authorIds) :
            new Query\Term(['authors.author_id' => $authorIds])
        ;

        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('authors');
        $nestedQuery->setQuery($queryTerm);

        $query->addMustNot($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $query
     */
    private function applyIdFilter(QueryParams $queryParams, Query\BoolQuery $query)
    {
        $bookId    = $queryParams->getFilterId();
        $queryTerm = is_array($bookId) ?
            new Query\Terms('book_id', $bookId) :
            new Query\Term(['book_id' => $bookId])
        ;

        $query->addMust($queryTerm);
    }

    /**
     * @param Query\BoolQuery $query
     */
    private function applyFeaturedHomeFilter(Query\BoolQuery $query)
    {
        $query->addMust(new Query\Term(['featured_home' => true]));
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $boolQuery
     */
    private function applyGenreFilter(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        $genreIds  = $queryParams->getFilterGenres();
        $queryTerm = is_array($genreIds) ?
            new Query\Terms('genres.genre_id', $genreIds) :
            new Query\Term(['genres.genre_id' => $genreIds])
        ;

        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('genres');
        $nestedQuery->setQuery($queryTerm);

        $boolQuery->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $boolQuery
     */
    private function applyAuthorFilter(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        $authorIds = $queryParams->getFilterAuthors();
        $queryTerm = is_array($authorIds) ?
            new Query\Terms('authors.author_id', $authorIds) :
            new Query\Term(['authors.author_id' => $authorIds])
        ;

        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('authors');
        $nestedQuery->setQuery($queryTerm);

        $boolQuery->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $boolQuery
     */
    private function applyTagFilter(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        $tagIds    = $queryParams->getFilterTags();
        $queryTerm = is_array($tagIds) ?
            new Query\Terms('tags.tag_id', $tagIds) :
            new Query\Term(['tags.tag_id' => $tagIds])
        ;

        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('tags');
        $nestedQuery->setQuery($queryTerm);

        $boolQuery->addMust($nestedQuery);
    }

    /**
     * @param QueryParams     $queryParams
     * @param Query\BoolQuery $boolQuery
     */
    private function applySequenceFilter(QueryParams $queryParams, Query\BoolQuery $boolQuery)
    {
        $sequenceIds = $queryParams->getFilterSequences();
        $queryTerm   = is_array($sequenceIds) ?
            new Query\Terms('sequence.sequence_id', $sequenceIds) :
            new Query\Term(['sequence.sequence_id' => $sequenceIds])
        ;

        $nestedQuery = new Query\Nested();
        $nestedQuery->setPath('sequence');
        $nestedQuery->setQuery($queryTerm);

        $boolQuery->addMust($nestedQuery);
    }
}
