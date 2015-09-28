<?php

namespace Monospice\SpicyRepositories\Test\Functional\Eloquent;

use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestDatabase;

/**
 * Creates a test database for testing repositories
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
class TestSchema
{
    public static function initialize($database)
    {
        $database->createTable('test_models', function($table) {
            $table->increments('id');
            $table->string('attribute1');
            $table->string('attribute2');
        });

        $database->createTable('related_models', function($table) {
            $table->increments('id');
            $table->string('attribute3');
            $table->integer('test_model_id')
                ->references('id')
                ->on('test_models');
        });
    }
}
