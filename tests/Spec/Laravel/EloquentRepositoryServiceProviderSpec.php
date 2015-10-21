<?php

namespace Spec\Monospice\SpicyRepositories\Laravel;

use Mockery;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Monospice\SpicyRepositories\Laravel\EloquentRepositoryServiceProvider;

class EloquentRepositoryServiceProviderSpec extends ObjectBehavior
{
    /**
     * The mock object of the Laravel application container
     *
     * @var Mockery
     */
    protected $container;

    function let()
    {
        $this->container = Mockery::mock('Illuminate\Container\Container');

        $this->beAnInstanceOf(
            'Spec\Monospice\SpicyRepositories\Laravel' .
            '\ConcreteEloquentRepositoryServiceProvider',
            [$this->container] // constructor arguments
        );
    }

    function letGo()
    {
        Mockery::close();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(
            'Monospice\SpicyRepositories\Laravel' .
            '\EloquentRepositoryServiceProvider'
        );
    }

    function it_binds_repository_classes_to_the_application_container()
    {
        $this->container->shouldReceive('bind')->withAnyArgs()->once();
        $this->register();
    }
}

/**
 * Stub to test the EloquentServiceProvider
 */
class ConcreteEloquentRepositoryServiceProvider
extends EloquentRepositoryServiceProvider
{
    /**
     * Stub to test the binding of views by this service provider
     *
     * @return void
     */
    protected function bindStubRepository()
    {
        $this->interface = 'Namespace\TestRepositoryInterface';

        return function () {
            return new \stdClass();
        };
    }
}
