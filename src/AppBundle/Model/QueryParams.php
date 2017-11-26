<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Model;

class QueryParams
{
    const SORT_NO                = 0;
    const SORT_ADDED_ON_DESC     = 1;
    const SORT_DATE_PUBLISH_DESC = 2;
    const SORT_RATING_DESC       = 3;

    /**
     * @var array|int
     */
    private $filterId;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $page;

    /**
     * @var array|int
     */
    private $filterGenres;

    /**
     * @var array|int
     */
    private $filterTags;

    /**
     * @var array|int
     */
    private $filterAuthors;

    /**
     * @var array|int
     */
    private $filterSequences;

    /**
     * @var array|int
     */
    private $filterExcludeBooks;

    /**
     * @var array|int
     */
    private $filterExcludeAuthors;

    /**
     * @var string
     */
    private $searchQuery;

    /**
     * @var bool
     */
    private $filterFeaturedHome;

    /**
     * @var bool
     */
    private $hasCover;

    /**
     * @var int
     */
    private $sort;

    /**
     * Initialize fields
     */
    public function __construct()
    {
        $this->page        = 1;
        $this->hasCover    = false;
        $this->sort        = self::SORT_NO;
        $this->searchQuery = null;
    }

    /**
     * @return array|int
     */
    public function getFilterId()
    {
        return $this->filterId;
    }

    /**
     * @param array|int $filterId
     *
     * @return QueryParams
     */
    public function setFilterId($filterId)
    {
        $this->filterId = $filterId;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getFilterGenres()
    {
        return $this->filterGenres;
    }

    /**
     * @param array|int $genreId
     *
     * @return QueryParams
     */
    public function setFilterGenres($genreId)
    {
        $this->filterGenres = $genreId;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getFilterTags()
    {
        return $this->filterTags;
    }

    /**
     * @param array|int $filterTags
     *
     * @return QueryParams
     */
    public function setFilterTags($filterTags)
    {
        $this->filterTags = $filterTags;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    /**
     * @param string $searchQuery
     *
     * @return QueryParams
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getFilterAuthors()
    {
        return $this->filterAuthors;
    }

    /**
     * @param array|int $filterAuthors
     *
     * @return QueryParams
     */
    public function setFilterAuthors($filterAuthors)
    {
        $this->filterAuthors = $filterAuthors;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getFilterSequences()
    {
        return $this->filterSequences;
    }

    /**
     * @param array|int $filterSequences
     *
     * @return QueryParams
     */
    public function setFilterSequences($filterSequences)
    {
        $this->filterSequences = $filterSequences;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getFilterExcludeBooks()
    {
        return $this->filterExcludeBooks;
    }

    /**
     * @param array|int $filterExcludeBooks
     *
     * @return QueryParams
     */
    public function setFilterExcludeBooks($filterExcludeBooks)
    {
        $this->filterExcludeBooks = $filterExcludeBooks;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getFilterExcludeAuthors()
    {
        return $this->filterExcludeAuthors;
    }

    /**
     * @param array|int $filterExcludeAuthors
     *
     * @return QueryParams
     */
    public function setFilterExcludeAuthors($filterExcludeAuthors)
    {
        $this->filterExcludeAuthors = $filterExcludeAuthors;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return QueryParams
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return QueryParams
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     *
     * @return QueryParams
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFilterFeaturedHome()
    {
        return $this->filterFeaturedHome;
    }

    /**
     * @return bool
     */
    public function hasCover()
    {
        return $this->hasCover;
    }

    /**
     * @param bool $hasCover
     *
     * @return QueryParams
     */
    public function setHasCover($hasCover)
    {
        $this->hasCover = $hasCover;

        return $this;
    }

    /**
     * @return QueryParams
     */
    public function setFilterFeaturedHome()
    {
        $this->filterFeaturedHome = true;

        return $this;
    }
}
