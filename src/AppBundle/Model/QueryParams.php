<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Model;

class QueryParams
{
    const SORT_DATE_DESC         = 1;
    const SORT_DATE_PUBLISH_DESC = 2;

    /**
     * @var int
     */
    private $filterId;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int|array
     */
    private $filterGenres;

    /**
     * @var int|array
     */
    private $filterTags;

    /**
     * @var int|array
     */
    private $filterAuthors;

    /**
     * @var int|array
     */
    private $filterSequences;

    /**
     * @var string
     */
    private $searchQuery;

    /**
     * @var integer
     */
    private $sort;

    /**
     * Initialize fields
     */
    public function __construct()
    {
        $this->size  = 20;
        $this->page  = 1;
        $this->start = 0;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return ($this->page - 1) * $this->size;
    }

    /**
     * @return int
     */
    public function getFilterId()
    {
        return $this->filterId;
    }

    /**
     * @param int $filterId
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
     * @param int|array $genreId
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
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param int $start
     *
     * @return QueryParams
     */
    public function setStart($start)
    {
        $this->start = $start;

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
}