<?php

namespace Monospice\SpicyRepositories\Laravel\Traits;

/**
 * Defines methods for applying criteria to Repository methods
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
trait HasCriteria
{
    /**
     * Contains the set of criteria
     *
     * @var array
     */
    protected $criteria;

    /**
     * True flag indicates that the criteria should be reset after each query
     * (default: true)
     *
     * @var bool
     */
    protected $rememberCriteria;

    // Inherit Doc from Interfaces\Criteria
    public function getCriteria()
    {
        return $this->criteria;
    }

    // Inherit Doc from Interfaces\Criteria
    public function addCriterion($criteria)
    {
        $this->criteria[] = $criteria;

        return $this;
    }

    // Inherit Doc from Interfaces\Criteria
    public function addCriteria($criteria)
    {
        return $this->addCriterion($criteria);
    }

    // Inherit Doc from Interfaces\Criteria
    public function applyCriteria($query)
    {
        foreach ($this->criteria as $applyCriteria) {
            $query = $applyCriteria($query);
        }

        if ($this->rememberCriteria === false) {
            $this->clearCriteria();
        }

        return $query;
    }

    // Inherit Doc from Interfaces\Criteria
    public function clearCriteria()
    {
        $this->criteria = [];

        return $this;
    }

    // Inherit Doc from Interfaces\Criteria
    public function rememberCriteria($shouldRemember = true)
    {
        $this->rememberCriteria = $shouldRemember;

        return $this;
    }
}
