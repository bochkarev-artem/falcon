<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Model;

class QueryParams
{
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
}