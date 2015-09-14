<?php

namespace Monospice\SpicyRepositories\Test\Functional\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * A generic Eloquent model used for testing
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
class TestModel extends Model
{
    protected $fillable = [
        'attribute1',
        'attribute2',
    ];

    public $timestamps = false;
}
