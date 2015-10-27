<?php

namespace Monospice\SpicyRepositories\Laravel\Traits;

use Monospice\SpicyIdentifiers\DynamicMethod;

/**
 * Enables a repository to define criteria methods
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
     * Flag indicates that the criteria should be reset after each query
     * (default: false)
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
    public function addCriterion(DynamicMethod $method, array $arguments = [])
    {
        $this->criteria[] = function($query) use ($method, $arguments) {
            array_unshift($arguments, $query);

            return $method->callOn($this, $arguments);
        };

        return $this;
    }

    // Inherit Doc from Interfaces\Criteria
    public function addCriteria(DynamicMethod $method, array $arguments = [])
    {
        return $this->addCriterion($method, $arguments);
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
