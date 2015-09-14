<?php

namespace Monospice\SpicyRepositories\Laravel;

use Illuminate\Support\ServiceProvider;

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
     * Return an associative array of repositories to bind into the service
     * container consisting of key/value pairs where the key is the fully
     * namespaced class name of the repository interface and the value is the
     * closure function that returns a new instance of the concrete repository
     *
     * Example:
     *
     *  protected function getRepositories() {
     *      return [
     *          'App\Repositories\Interfaces\UserRepository' => function() {
     *              return new \App\Repositories\UserRepository;
     *          },
     *          'App\Repositories\Interfaces\MessageRepository' => function() {
     *              return new \App\Repositories\MessageRepository;
     *          }
     *      ];
     *  }
     *
     * @return array The array of repository bindings
     */
    abstract protected function repositories();

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
     */
    public function register()
    {
        $repositories = $this->repositories();

        foreach ($repositories as $interface => $instanceClosure) {
            $this->app->bind(
                $interface,
                function ($app) use ($instanceClosure) {
                    return $instanceClosure();
                }
            );
        }
    }
}
