<?php

namespace Monospice\SpicyRepositories\Test\Functional\Eloquent;

/**
 * Provides test data for repository tests
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
class TestData
{
    const TABLE = 'test_models';
    const RELATED_TABLE = 'related_models';

    public static function seedTestModels($database)
    {
        $data = [
            [
                'id' => '1',
                'attribute1' => 'value1',
                'attribute2' => 'same',
            ],
            [
                'id' => '2',
                'attribute1' => 'value3',
                'attribute2' => 'value4',
            ],
            [
                'id' => '3',
                'attribute1' => 'value5',
                'attribute2' => 'same',
            ],
        ];

        $database->seed(self::TABLE, $data);

        return $data;
    }

    public static function seedRelatedModels($database)
    {
        $data = [
            [
                'id' => '1',
                'attribute3' => 'related',
                'test_model_id' => '1',
            ],
        ];

        $database->seed(self::RELATED_TABLE, $data);

        return $data;
    }
}
