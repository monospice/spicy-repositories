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

    /**
     * A helper method that gets the attribute from an argument passed to a
     * method if the argument is an instance of a model. Useful for criteria
     * methods that can accept either a model or an attribute
     *
     * @param object|mixed $modelOrAttribute The model instance or a value of
     * the expected type of the attribute
     * @param string       $attribute        The attribute name on the model
     *
     * @return mixed The value of the attribute
     */
    abstract protected static function getAttributeIfModel(
        $modelOrAttribute,
        $attribute = 'id'
    );
}
