<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Model;

class Pagination
{
    /**
     * @var integer
     */
    private $currentPage;

    /**
     * @var integer
     */
    private $perPage;

    /**
     * @var integer
     */
    private $pageRange;

    /**
     * @var array
     */
    private $viewData;

    /**
     * @var integer
     */
    private $totalPages = 1;

    /**
     * @param int $currentPage
     * @param int $perPage
     * @param int $pageRange
     */
    public function __construct($currentPage, $perPage, $pageRange = 10)
    {
        $this->currentPage = $currentPage;
        $this->perPage     = $perPage;
        $this->pageRange   = $pageRange;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        $offset = ($this->currentPage - 1) * $this->perPage;

        return $offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return $this->viewData;
    }

    /**
     * @param int $count
     */
    public function paginate($count)
    {
        $this->totalPages = intval(ceil($count / $this->perPage));
        $current = $this->currentPage;

        if ($this->pageRange > $this->totalPages) {
            $this->pageRange = $this->totalPages;
        }

        $delta = ceil($this->pageRange / 2);

        if ($current - $delta > $this->totalPages - $this->pageRange) {
            $pages = range($this->totalPages - $this->pageRange + 1, $this->totalPages);
        }
        else {
            if ($current - $delta < 0) {
                $delta = $current;
            }

            $offset = $current - $delta;
            $pages  = range($offset + 1, $offset + $this->pageRange);
        }

        $viewData = [
            'last'        => $this->totalPages,
            'current'     => $current,
            'total_pages' => $this->totalPages,
            'prev'        => $current - 1 > 0 ? $current - 1 : null,
            'next'        => $current + 1 <= $this->totalPages ? $current + 1 : null,
            'pages'       => $pages,
        ];

        $this->viewData = $viewData;
    }
}
