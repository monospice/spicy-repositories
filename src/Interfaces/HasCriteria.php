<?php

namespace Monospice\SpicyRepositories\Interfaces;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;

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
interface HasCriteria
{
    /**
     * Get the array of criteria closures
     *
     * @return array The array of criteria closures
     */
    public function getCriteria();

    /**
     * Add a criterion to the chain of criteria
     *
     * @param Monospice\SpicyIdentifiers\DynamicMethod $method The method
     * object representing the method of the criterion to apply
     * @param array $arguments The array of arguments to pass to the criterion
     * method
     *
     * @return $this The current instance of the Repository for method chaining
     */
    public function addCriterion(DynamicMethod $method, array $arguments);

    /**
     * An alias for addCriterion()
     *
     * @param Monospice\SpicyIdentifiers\DynamicMethod $method The method
     * object representing the method of the criterion to apply
     * @param array $arguments The array of arguments to pass to the criterion
     * method
     *
     * @return $this The current instance of the Repository for method chaining
     */
    public function addCriteria(DynamicMethod $method, array $arguments);

    /**
     * Apply the criteria to the query
     *
     * @param mixed $query The query object to apply the criteria to
     *
     * @return mixed The query object constrained by the applied criteria
     */
    public function applyCriteria(QueryBuilder $query);

    /**
     * Remove all criteria from the chain
     *
     * @return $this The current instance of the Repository for method chaining
     */
    public function clearCriteria();

    /**
     * Require the repository to remember the applied criteria for
     * subsequent queries
     *
     * @param bool $shouldRemember If true, the repository will remember the
     * applied criteria for subsequent queries (default: true)
     *
     * @return $this The current instance of the Repository for method chaining
     */
    public function rememberCriteria($shouldRemember = true);
}
