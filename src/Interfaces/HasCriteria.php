<?php

namespace Monospice\SpicyRepositories\Interfaces;

/**
 * Defines methods for applying criteria to Repository methods
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
interface HasCriteria
{
    /**
     * Get the array of criteria closures
     *
     * @return array The array of criteria closures
     */
    public function getCriteria();

    /**
     * Add a criterion to the chain
     *
     * @param function $criteria The closure representing the criteria to add
     *
     * @return mixed The current instance of the Repository for method chaining
     */
    public function addCriterion($criteria);

    /**
     * An alias for addCriterion()
     *
     * @param function $criteria The closure representing the criteria to add
     *
     * @return mixed The current instance of the Repository for method chaining
     */
    public function addCriteria($criteria);

    /**
     * Apply the criteria to the query
     *
     * @param mixed $query The query object to apply the criteria to
     *
     * @return mixed The query object constrained by the applied criteria
     */
    public function applyCriteria($query);

    /**
     * Remove all criteria from the chain
     *
     * @return mixed The current instance of the Repository for method chaining
     */
    public function clearCriteria();

    /**
     * Require the repository to remember the applied criteria for
     * subsequent queries
     *
     * @param bool $shouldRemember If true, the repository will remember the
     * applied criteria for subsequent queries (default: true)
     *
     * @return mixed The current instance of the Repository for method chaining
     */
    public function rememberCriteria($shouldRemember = true);
}
