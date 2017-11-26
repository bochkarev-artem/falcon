<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Model\Pagerfanta;

use Elastica\Query;
use Elastica\ResultSet;
use Elastica\SearchableInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ElasticaAdapter implements AdapterInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var ResultSet
     */
    private $resultSet;

    /**
     * @var SearchableInterface
     */
    private $searchable;

    public function __construct(SearchableInterface $searchable, Query $query)
    {
        $this->searchable = $searchable;
        $this->query      = $query;
    }

    /**
     * Returns the number of results.
     *
     * @return int The number of results.
     */
    public function getNbResults()
    {
        $maxNumber = 9980;

        if (!$this->resultSet) {
            $actualNumber = $this->searchable->search($this->query)->getTotalHits();
        } else {
            $actualNumber = $this->resultSet->getTotalHits();
        }

        return min($maxNumber, $actualNumber);
    }

    /**
     * Returns the Elastica ResultSet. Will return null if getSlice has not yet been
     * called.
     *
     * @return null|ResultSet
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset The offset.
     * @param int $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        return $this->resultSet = $this->searchable->search($this->query, [
            'from' => $offset,
            'size' => $length,
        ]);
    }
}
