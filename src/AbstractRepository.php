<?php

namespace Monospice\SpicyRepositories;

use Monospice\SpicyRepositories\Interfaces;

/**
 * Extend this abstract class to create repositories
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
abstract class AbstractRepository implements Interfaces\Repository
{
    /**
     * The result of the last create, update, or delete query
     *
     * @var mixed
     */
    protected $result;

    /**
     * Start a new select query on the model, applying any existing criteria
     *
     * @return mixed The new query object
     */
    abstract protected function select();

    // Inherit Doc from Interfaces\Repository
    public function setResult($result)
    {
        $this->result = $result;
    }

    // Inherit Doc from Interfaces\Repository
    public function getResult()
    {
        return $this->result;
    }
}
