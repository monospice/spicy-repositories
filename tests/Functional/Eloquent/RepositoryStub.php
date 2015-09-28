<?php

namespace Monospice\SpicyRepositories\Test\Functional\Eloquent;

use Monospice\SpicyRepositories\Laravel\EloquentRepository;

/**
 * A repository stub used for testing Eloquent Repositories
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
class RepositoryStub extends EloquentRepository
{
    /**
     * The repository's relationships
     *
     * @var array
     */
    protected $related = ['relatedModel'];
}
