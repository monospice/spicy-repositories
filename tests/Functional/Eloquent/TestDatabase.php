<?php

namespace Monospice\SpicyRepositories\Test\Functional\Eloquent;

use Monospice\SpicyRepositories\Test\Functional\Eloquent\DatabaseTester;

/**
 * Sets up a test database connection using Eloquent
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
class TestDatabase
{
    public static function initialize()
    {
        $database = new DatabaseTester([
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../../_data/repository_test_db.sqlite',
            'prefix' => '',
        ]);

        return $database;
    }
}
