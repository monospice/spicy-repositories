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
 */
interface BasicCriteria extends Interfaces\HasCriteria
{
    /**
     * Include the specified columns in the result set
     *
     * @param string|array $columns The columns to include in the result set
     *
     * @return \Monospice\SpicyRepositories\Interfaces\Repository The current
     * repository used for chaining methods
     */
    public function only($columns);

    /**
     * Exclude the specified columns from the result set
     *
     * @param string|array $columns The columns to include in the result set
     *
     * @return \Monospice\SpicyRepositories\Interfaces\Repository The current
     * repository used for chaining methods
     */
    public function exclude($columns);

    /**
     * Limit the result set to the specified number of records
     *
     * @param int $limit The maximum number of records to return
     *
     * @return \Monospice\SpicyRepositories\Interfaces\Repository The current
     * repository used for chaining methods
     */
    public function limit($limit);

    /**
     * Order the result set by the specified column
     *
     * @param string $column    The column to sort the result set by
     * @param string $direction The order to sort the result set by
     *
     * @return \Monospice\SpicyRepositories\Interfaces\Repository The current
     * repository used for chaining methods
     */
    public function orderBy($column, $direction = 'asc');

    /**
     * Eager load the specified relationships of the model
     *
     * @param string|array $related The names of the relationships to load
     *
     * @return \Monospice\SpicyRepositories\Interfaces\Repository The current
     * repository used for chaining methods
     */
    public function with($related);

    /**
     * Eager load all the relationships of the model
     *
     * @return \Monospice\SpicyRepositories\Interfaces\Repository The current
     * repository used for chaining methods
     */
    public function withRelated();
}
