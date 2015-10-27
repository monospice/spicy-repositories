<?php

namespace Monospice\SpicyRepositories\Interfaces;

/**
 * Performs an update operation or a create operation if the record doesn't
 * exist
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
interface UpdateOrCreate
{
    /**
     * Set the criteria to determine if the record exists
     *
     * @param array $where An associative array of column => value pairs used
     * to match records for update or set for create
     *
     * @return $this The original instance of this class for method chaining
     */
    public function where(array $where);

    /**
     * An alias for where()
     *
     * @param array $where An associative array of column => value pairs used
     * to match records for update or set for create
     *
     * @return $this The original instance of this class for method chaining
     */
    public function orWhere(array $where);

    /**
     * Set the criteria to determine if the record exists
     *
     * @param array $set An associative array of column => value pairs to set
     *
     * @return \Monospice\SpicyRepositories\Interfaces\Repository The original
     * repository instance for method chaining
     */
    public function set(array $set);
}
