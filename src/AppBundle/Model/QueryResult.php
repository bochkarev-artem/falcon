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
     * @return int
     */
    public function getTotalHits()
    {
        return $this->elasticResultSet->getTotalHits();
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
