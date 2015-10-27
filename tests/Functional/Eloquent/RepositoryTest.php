<?php

namespace Monospice\SpicyRepositories\Tests\Functional\Eloquent;

use PHPUnit_Framework_TestCase;

use Monospice\SpicyRepositories\Test\Functional\Eloquent\RepositoryStub;
use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestModel;
use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestData;
use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestDatabase;
use Monospice\SpicyRepositories\Test\Functional\Eloquent\TestSchema;

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
        $this->data = TestData::seedTestModels($this->database);
        TestData::seedRelatedModels($this->database);

        $this->repository = new RepositoryStub(new TestModel);
    }

    public function testGetAll()
    {
        $result = $this->repository->getAll();

        $this->assertSame($this->data, $result->toArray());
    }

    public function testPaginateAll()
    {
        $page1 = [$this->data[0]];
        $page2 = [$this->data[1]];

        $result = $this->repository->paginateAll(1);
        $this->assertSame($page1, $result->toArray()['data']);

        $result = $this->repository->paginateAll(1, 'page', 2);
        $this->assertSame($page2, $result->toArray()['data']);
    }

    public function testGetFirst()
    {
        $result = $this->repository->getFirst();

        $this->assertSame($this->data[0], $result->toArray());
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
        $page1 = [$this->data[0]];
        $page2 = [$this->data[2]];

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
        $input = [
            'attribute1' => 'new',
            'attribute2' => 'new',
        ];

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

        $result = $this->repository->update(1, $input)->getResult();

        $this->database->seeIn('test_models', [
            'id' => '1',
            'attribute1' => 'changed',
        ]);
    }

    public function testUpdateMultiple()
    {
        $input = ['attribute1' => 'changed'];

        $result = $this->repository
            ->update('same', $input, 'attribute2')
            ->getResult()
            ->toArray();

        $this->database->seeIn('test_models', [
            'id' => '1',
            'attribute1' => 'changed',
        ]);
        $this->database->seeIn('test_models', [
            'id' => '3',
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

        $result = $this->repository
            ->exclude(['id', 'attribute2'])
            ->getAll();

        $this->assertSame($expected, $result->toArray());
    }

    public function testCriterionLimit()
    {
        $expected = [$this->data[0]];

        $result = $this->repository->limit(1)->getAll();

        $this->assertSame($expected, $result->toArray());
    }

    public function testCriterionOrderBy()
    {
        $expected = [
            $this->data[0],
            $this->data[2],
            $this->data[1],
        ];

        $result = $this->repository->orderBy('attribute2')->getAll();

        $this->assertSame($expected, $result->toArray());
    }

    public function testCriterionWith()
    {
        $expected = 'related';

        $result = $this->repository->with('relatedModel')->getAll()
            ->first()->relatedModel->attribute3;

        $this->assertSame($expected, $result);
    }

    public function testCriterionWithRelated()
    {
        $expected = 'related';

        $result = $this->repository->withRelated()->getAll()
            ->first()->relatedModel->attribute3;

        $this->assertSame($expected, $result);
    }
}
