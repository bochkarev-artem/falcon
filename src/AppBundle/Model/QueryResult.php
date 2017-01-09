<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Model;

use Elastica\ResultSet;

class QueryResult
{
    /**
     * @var \Elastica\ResultSet
     */
    private $elasticResultSet;

    /**
     * @var int
     */
    private $maxTotalHits;

    /**
     * @param \Elastica\ResultSet $elasticResultSet
     */
    public function __construct(ResultSet $elasticResultSet)
    {
        $this->elasticResultSet = $elasticResultSet;
    }

    /**
     * @return \Elastica\Result[]
     */
    public function getResults()
    {
        return $this->elasticResultSet->getResults();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->elasticResultSet->count();
    }

    /**
     * @param int $maxTotalHits
     *
     * @return QueryResult
     */
    public function setMaxTotalHits($maxTotalHits)
    {
        $this->maxTotalHits = $maxTotalHits;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalHits()
    {
        $totalHits = $this->elasticResultSet->getTotalHits();
        if (isset($this->maxTotalHits) && $totalHits > $this->maxTotalHits) {
            $totalHits = $this->maxTotalHits;
        }

        return $totalHits;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getAggregation($name)
    {
        if ($this->elasticResultSet->hasAggregations()) {
            return $this->elasticResultSet->getAggregation($name);
        }

        return [];
    }
}
