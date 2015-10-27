<?php

namespace Monospice\SpicyRepositories\Interfaces;

use Monospice\SpicyRepositories\Interfaces;

/**
 * Defines methods for adding basic criteria to Repository classes
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 *
 * @method $this only($columns) Include only the specified columns
 * @method $this exclude($columns) Include all but the specified columns
 * @method $this limit($number) Limit the result set to the specified number of
 * records
 * @method $this orderBy($column, $direction = 'asc') Order the result set by
 * the specified column
 * @method $this with($related) Eager load the specified relationships
 * @method $this withRelated() Eager load all the relationships defined in the
 * repository
 */
interface BasicCriteria extends Interfaces\HasCriteria
{
    /**
     * Include only the specified columns in the result set
     *
     * @param mixed        $query   The query object to apply the criterion to
     * @param string|array $columns The columns to include in the result set
     *
     * @return mixed The passed query object
     */
    public function onlyCriterion($query, $columns);

    /**
     * Exclude the specified columns from the result set
     *
     * @param mixed        $query   The query object to apply the criterion to
     * @param string|array $columns The columns to include in the result set
     *
     * @return mixed The passed query object
     */
    public function excludeCriterion($query, $columns);

    /**
     * Limit the result set to the specified number of records
     *
     * @param mixed $query The query object to apply the criterion to
     * @param int   $limit The maximum number of records to return
     *
     * @return mixed The passed query object
     */
    public function limitCriterion($query, $limit);

    /**
     * Order the result set by the specified column
     *
     * @param mixed  $query     The query object to apply the criterion to
     * @param string $column    The column to sort the result set by
     * @param string $direction The order to sort the result set by
     *
     * @return mixed The passed query object
     */
    public function orderByCriterion($query, $column, $direction = 'asc');

    /**
     * Eager load the specified relationships of the model
     *
     * @param mixed        $query   The query object to apply the criterion to
     * @param string|array $related The names of the relationships to load
     *
     * @return mixed The passed query object
     */
    public function withCriterion($query, $related);

    /**
     * Eager load all the relationships of the model
     *
     * @param mixed $query The query object to apply the criterion to
     *
     * @return mixed The passed query object
     */
    public function withRelatedCriterion($query);
}
