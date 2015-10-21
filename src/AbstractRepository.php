<?php

namespace Monospice\SpicyRepositories;

use Monospice\SpicyIdentifiers\DynamicMethod;

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
     * Create a new instance of this class
     */
    public function __construct()
    {
        if (property_exists($this, 'criteria')) {
            $this->clearCriteria();
            $this->rememberCriteria(false);
        }
    }

    /**
     * Start a new query on the model, applying any existing criteria
     *
     * @return mixed The new query object
     */
    abstract public function query();

    // Inherit Doc from Interfaces\Repository
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Proxy dynamic method calls to criteria methods
     *
     * @param string $methodName The called method name
     * @param array  $arguments  The called method arguments
     *
     * @return mixed The return value from the criterion method
     *
     * @throws \BadMethodCallException If the method does not exist in the
     * repository
     */
    public function __call($methodName, array $arguments)
    {
        if (! property_exists($this, 'criteria')) {
            throw new \BadMethodCallException(
                'The current implementation of this repository class does ' .
                'not support criteria. Ensure the "HasCriteria" trait and ' .
                'interface are declared in the extending class or parent.'
            );
        }

        $method = DynamicMethod::load($methodName)->append('Criterion');

        if ($method->existsOn($this)) {
            return $this->addCriterion($method, $arguments);
        }

        $method->pop()->append('Criteria');

        if ($method->existsOn($this)) {
            return $this->addCriterion($method, $arguments);
        }

        throw new \BadMethodCallException(
            'The criteria method [' . $method . '] does not exist.'
        );
    }
}
