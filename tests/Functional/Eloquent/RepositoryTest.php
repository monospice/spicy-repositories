<?php

namespace Monospice\SpicyRepositories\Tests\Functional\Eloquent;

use PHPUnit_Framework_TestCase;

use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestModel;
use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestData;
use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestDatabase;
use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestSchema;

use Monospice\SpicyRepositories\Laravel\EloquentRepository;

class EloquentRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * The repository to test
     *
     * @var Monospice\SpicyRepositories\Laravel\EloquentRepository
     */
    protected $repository;

    /**
     * The TestDatabase instance to use for database operations
     *
     * @var Illuminate\SpicyRepositories\Test\Functional\TestDatabase
     */
    protected $database;

    /**
     * The set of data tested
     *
     * @var array
     */
    protected $data;

    /**
     * Run this setup before each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->database = TestDatabase::initialize();
        TestSchema::initialize($this->database);
        $this->data = TestData::seed($this->database);

        $this->repository = new EloquentRepository(new TestModel);
    }

    public function testGetAll()
    {
        $result = $this->repository->getAll();

        $this->assertSame($this->data, $result->toArray());
    }

    public function testPaginateAll()
    {
        $page1 = [[
            'id' => '1',
            'attribute1' => 'value1',
            'attribute2' => 'same',
        ]];
        $page2 = [[
            'id' => '2',
            'attribute1' => 'value3',
            'attribute2' => 'value4',
        ]];


        $result = $this->repository->paginateAll(1);
        $this->assertSame($page1, $result->toArray()['data']);

        $result = $this->repository->paginateAll(1, 'page', 2);
        $this->assertSame($page2, $result->toArray()['data']);
    }

    public function testGet()
    {
        $result = $this->repository->get(1);

        $this->assertSame($this->data[0], $result->toArray());
    }

    public function testGetBy()
    {
        $allColumns[] = $this->data[0];
        $allColumns[] = $this->data[2];
        $result = $this->repository->getBy('attribute2', 'same');

        $this->assertSame($allColumns, $result->toArray());
    }

    public function testPaginateBy()
    {
        $page1 = [[
            'id' => '1',
            'attribute1' => 'value1',
            'attribute2' => 'same',
        ]];
        $page2 = [[
            'id' => '3',
            'attribute1' => 'value5',
            'attribute2' => 'same',
        ]];


        $result = $this->repository->paginateBy('attribute2', 'same', 1);
        $this->assertSame($page1, $result->toArray()['data']);

        $result = $this->repository->paginateBy('attribute2', 'same', 1, 'page', 2);
        $this->assertSame($page2, $result->toArray()['data']);
    }

    public function testGetList()
    {
        $list = ['1' => 'value1', '2' => 'value3', '3' => 'value5'];
        $result = $this->repository->getList('attribute1');

        $this->assertSame($list, $result);
    }

    public function testCreate()
    {
        $input = ['attribute1' => 'new', 'attribute2' => 'new'];
        $newModel = $this->repository->create($input)->getResult();

        $this->assertSame(4, $newModel->id);
        $this->database->seeIn('test_models', [
            'id' => '4',
            'attribute1' => 'new'
        ]);
    }

    public function testUpdate()
    {
        $input = ['attribute1' => 'changed'];
        $numChanged = $this->repository->update(1, $input)->getResult();

        $this->assertSame(1, $numChanged);
        $this->database->seeIn('test_models', [
            'id' => '1',
            'attribute1' => 'changed',
        ]);
    }

    public function testDelete()
    {
        $numChanged = $this->repository->delete(1)->getResult();

        $this->assertSame(1, $numChanged);
        $this->database->dontSeeIn('test_models', [
            'id' => '1',
        ]);
    }

    public function testCriterionOnly()
    {
        $expected = [
            ['attribute1' => 'value1'],
            ['attribute1' => 'value3'],
            ['attribute1' => 'value5'],
        ];
        $result = $this->repository->only('attribute1')->getAll();

        $this->assertSame($expected, $result->toArray());
    }

    public function testCriterionExclude()
    {
        $expected = [
            ['attribute1' => 'value1'],
            ['attribute1' => 'value3'],
            ['attribute1' => 'value5'],
        ];

        $result = $this->repository->exclude(['id', 'attribute2'])->getAll();

        $this->assertSame($expected, $result->toArray());
    }

    public function testCriterionLimit()
    {
        $expected = [[
            'id' => '1',
            'attribute1' => 'value1',
            'attribute2' => 'same',
        ]];

        $result = $this->repository->limit(1)->getAll();

        $this->assertSame($expected, $result->toArray());
    }

    public function testCriterionOrderBy()
    {
        $expected = [
            [
                'id' => '1',
                'attribute1' => 'value1',
                'attribute2' => 'same',
            ],
            [
                'id' => '3',
                'attribute1' => 'value5',
                'attribute2' => 'same',
            ],
            [
                'id' => '2',
                'attribute1' => 'value3',
                'attribute2' => 'value4',
            ],
        ];

        $result = $this->repository->orderBy('attribute2')->getAll();

        $this->assertSame($expected, $result->toArray());
    }
}
