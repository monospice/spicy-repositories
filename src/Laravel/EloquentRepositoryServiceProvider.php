<?php

namespace Monospice\SpicyRepositories\Laravel;

use Closure;
use Illuminate\Support\ServiceProvider;
use Monospice\SpicyIdentifiers\DynamicMethod;

/**
 * Binds the repository into the container
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
abstract class EloquentRepositoryServiceProvider extends ServiceProvider
{
    /**
     * The interface name set by a repository binding method that binds to
     * the next concrete repository
     *
     * @var string
     */
    protected $interface;

    /**
     * Boot the service
     *
     * @return void
     */
    public function boot()
    {
        // nothing to do
    }

    /**
     * Bind the repository interfaces into the service container
     *
     * @return void
     *
     * @throws \RuntimeException If the repository binding method does not
     * define an interface to bind the concrete repository instance to
     */
    public function register()
    {
        $this->callRepositoryBindingMethods();
    }

    /**
     * Calls each of the repository binding methods
     *
     * @return void
     *
     * @throws \RuntimeException If the repository binding method does not
     * define an interface to bind the concrete repository instance to
     */
    protected function callRepositoryBindingMethods()
    {
        $classMethods = get_class_methods($this);

        foreach ($classMethods as $methodName) {
            $method = DynamicMethod::parse($methodName);

            if ($method->startsWith('bind')
                && $method->endsWith('Repository')
            ) {
                $this->registerRepository($method->invokeOn($this));
            }
        }
    }

    /**
     * Register a repository with the application container
     *
     * @param Closure $repositoryClosure The anonymous function that returns
     * the concrete instance of the repository to use
     *
     * @return void
     *
     * @throws \RuntimeException If the repository binding method does not
     * define an interface to bind the concrete repository instance to
     */
    protected function registerRepository(Closure $repositoryClosure)
    {
        if ($this->interface === null) {
            throw new \RuntimeException(
                'No repository interface specified in the [' .
                $repositoryMethod . '] method. Please declare the interface ' .
                'for this repository by setting "$this->interface" in the ' .
                'repository binding method.'
            );
        }

        $this->app->bind($this->interface, $repositoryClosure);

        $this->interface = null;
    }
}
