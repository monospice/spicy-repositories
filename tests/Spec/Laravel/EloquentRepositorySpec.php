<?php

namespace Spec\Monospice\SpicyRepositories\Laravel;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mockery;

class EloquentRepositorySpec extends ObjectBehavior
{
    // Test Data
    protected $items;

    // Eloquent Mocks
    protected $connection;
    protected $schema;
    protected $paginator;
    protected $collection;
    protected $builder;
    protected $model;

    function let()
    {
        $this->items = [
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

        $this->connection = Mockery::mock('Illuminate\Database\Connection');
        $this->schema = Mockery::mock('Illuminate\Database\Schema\Builder');
        $this->paginator = Mockery::mock('Illuminate\Pagination\Paginator');
        $this->collection = Mockery::mock(
            'Illuminate\Database\Eloquent\Collection'
        );
        $this->builder = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');

        $this->beConstructedWith($this->model);
    }

    function letGo()
    {
        Mockery::close();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(
            'Monospice\SpicyRepositories\Laravel\EloquentRepository'
        );
    }

    function it_gets_all_records()
    {
        $this->collection->shouldReceive('toArray')->once()
            ->andReturn($this->items);
        $this->builder->shouldReceive('get')->once()
            ->andReturn($this->collection);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $result = $this->getAll();
        $result->shouldHaveType('Illuminate\Database\Eloquent\Collection');
        $result->toArray()->shouldEqual($this->items);
    }

    function it_paginates_all_records()
    {
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);
        $this->builder->shouldReceive('paginate')->with(1, ['*'], 'page', 1)
            ->once()->andReturn($this->paginator);
        $this->paginator->shouldReceive('toArray')->once()
            ->andReturn($this->items[0]);

        $result = $this->paginateAll(1, 'page', 1);
        $result->shouldHaveType('Illuminate\Pagination\Paginator');
        $result->toArray()->shouldEqual($this->items[0]);
    }

    function it_gets_a_record_by_the_id()
    {
        $this->collection->shouldReceive('toArray')->once()
            ->andReturn($this->items[0]);
        $this->builder->shouldReceive('find')->once()
            ->andReturn($this->collection);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $result = $this->get(0);
        $result->shouldHaveType('Illuminate\Database\Eloquent\Collection');
        $result->toArray()->shouldEqual($this->items[0]);
    }

    function it_gets_a_record_by_the_value_of_a_column()
    {
        $resultSet = [
            $this->items[0],
            $this->items[2],
        ];

        $this->collection->shouldReceive('toArray')->once()
            ->andReturn($resultSet);
        $this->builder->shouldReceive('where')->with('attribute2', '=', 'same')
            ->once()->andReturn($this->builder);
        $this->builder->shouldReceive('get')->once()
            ->andReturn($this->collection);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $result = $this->getBy('attribute2', 'same');
        $result->shouldHaveType('Illuminate\Database\Eloquent\Collection');
        $result->toArray()->shouldEqual($resultSet);
    }

    function it_paginates_records_selected_by_the_value_of_a_column()
    {
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);
        $this->builder->shouldReceive('where')->with('id', '=', 1)->once()
            ->andReturn($this->builder);
        $this->builder->shouldReceive('paginate')->with(1, ['*'], 'page', 1)
            ->once()->andReturn($this->paginator);
        $this->paginator->shouldReceive('toArray')->once()
            ->andReturn($this->items[0]);

        $result = $this->paginateBy('id', 1, 1, 'page', 1);
        $result->shouldHaveType('Illuminate\Pagination\Paginator');
        $result->toArray()->shouldEqual($this->items[0]);
    }

    function it_gets_a_list_of_records_as_an_associative_array()
    {
        $list = [
            0 => 'value1',
            1 => 'value3',
            2 => 'value5',
        ];

        $this->builder->shouldReceive('lists')->once()->andReturn($list);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $this->getList('attribute1')->shouldEqual($list);
    }

    function it_creates_a_new_record_and_gets_result()
    {
        $input = [
            'attribute1' => 'value7',
            'attribute2' => 'value8',
        ];
        $newData = [
            'id' => '4',
            'attribute1' => 'value7',
            'attribute2' => 'value8',
        ];

        $this->model->shouldReceive('create')->with($input)
            ->andReturn($this->model);
        $this->model->shouldReceive('toArray')->once()
            ->andReturn($newData);

        $result = $this->create($input);
        $result->shouldHaveType(
            'Monospice\SpicyRepositories\Laravel\EloquentRepository'
        );
        $result->getResult()->toArray()->shouldReturn($newData);
    }

    function it_updates_a_record_and_gets_result()
    {
        $input = ['attribute1' => 'changed'];

        $this->model->shouldReceive('where')->with('id', '=', 1)
            ->andReturn($this->builder);
        $this->builder->shouldReceive('count')->twice()->andReturn(1);
        $this->builder->shouldReceive('first')->once()
            ->andReturn($this->model);
        $this->model->shouldReceive('update')->with($input)
            ->andReturn($this->model);

        $result = $this->update(1, $input);
        $result->shouldHaveType(
            'Monospice\SpicyRepositories\Laravel\EloquentRepository'
        );
        $result->getResult()->shouldReturn($this->model);
    }

    function it_deletes_a_record_and_gets_result()
    {
        $numRecordsChanged = 1;

        $this->builder->shouldReceive('where')->with('id', '=', 1)
            ->andReturn($this->builder);
        $this->builder->shouldReceive('delete')->once()
            ->andReturn($numRecordsChanged);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $result = $this->delete(1);
        $result->shouldHaveType(
            'Monospice\SpicyRepositories\Laravel\EloquentRepository'
        );
        $result->getResult()->shouldReturn($numRecordsChanged);
    }

    function it_gets_a_list_of_criteria_for_a_query()
    {
        $this->getCriteria()->shouldReturn([]);
    }

    function it_adds_a_criterion_to_a_query()
    {
        $criteria = [function($query) { return $query; }];

        $this->addCriterion($criteria[0])->shouldReturn($this);
        $this->getCriteria()->shouldReturn($criteria);
        $this->addCriteria($criteria[0])->shouldReturn($this);
        $this->getCriteria()->shouldReturn([$criteria[0], $criteria[0]]);
    }

    function it_clears_a_set_of_criteria()
    {
        $criteria = [function($query) { return $query; }];

        $this->addCriterion($criteria[0])->clearCriteria()->shouldReturn($this);
        $this->getCriteria()->shouldReturn([]);
    }

    function it_applies_a_set_of_criteria_to_a_query()
    {
        $criteria = [function($query) {
            return $query->limit(5);
        }];

        $this->builder->shouldReceive('limit')->with('5')
            ->andReturn($this->builder);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $this->addCriterion($criteria[0]);
        $this->query()->shouldReturn($this->builder);

        $this->getCriteria()->shouldReturn([]);
    }

    function it_remembers_a_set_of_criteria()
    {
        $criteria = [function($query) { return $query; }];

        $this->model->shouldReceive('newQuery')->twice()
            ->andReturn($this->builder);

        $this->rememberCriteria();
        $this->addCriterion($criteria[0]);
        $this->query();
        $this->getCriteria()->shouldReturn($criteria);

        $this->rememberCriteria(false);
        $this->query();
        $this->getCriteria()->shouldReturn([]);
    }

    function it_applies_a_criterion_to_retrieve_only_specified_columns()
    {
        $oneColumn = [
            ['attribute1' => 'value1'],
            ['attribute1' => 'value3'],
            ['attribute1' => 'value5'],
        ];

        $this->collection->shouldReceive('toArray')->once()
            ->andReturn($oneColumn);
        $this->builder->shouldReceive('select')->with(['attribute1'])
            ->andReturn($this->builder);
        $this->builder->shouldReceive('get')->once()
            ->andReturn($this->collection);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $result = $this->only('attribute1')->getAll();
        $result->shouldHaveType('Illuminate\Database\Eloquent\Collection');
        $result->toArray()->shouldEqual($oneColumn);
    }

    function it_applies_a_criterion_to_retrieve_all_but_the_specified_columns()
    {
        $columnListing = ['id', 'attribute1', 'attribute2'];
        $includedColumns = ['id', 'attribute1'];
        $resultSet = [
            ['id' => '1', 'attribute1' => 'value1'],
            ['id' => '2', 'attribute1' => 'value3'],
            ['id' => '3', 'attribute1' => 'value5'],
        ];

        $this->schema->shouldReceive('getColumnListing')->with('test_models')
            ->andReturn($columnListing);
        $this->connection->shouldReceive('getSchemaBuilder')->once()
            ->andReturn($this->schema);
        $this->collection->shouldReceive('toArray')->once()
            ->andReturn($resultSet);
        $this->builder->shouldReceive('getQuery')->once()
            ->andReturn($this->builder);
        $this->builder->shouldReceive('getConnection')->once()
            ->andReturn($this->connection);
        $this->builder->shouldReceive('getModel')->once()
            ->andReturn($this->model);
        $this->builder->shouldReceive('select')->with($includedColumns)->once()
            ->andReturn($this->builder);
        $this->builder->shouldReceive('get')->once()
            ->andReturn($this->collection);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);
        $this->model->shouldReceive('getTable')->once()
            ->andReturn('test_models');

        $result = $this->exclude('attribute2')->getAll();
        $result->shouldHaveType('Illuminate\Database\Eloquent\Collection');
        $result->toArray()->shouldEqual($resultSet);
    }

    function it_applies_a_criterion_to_limit_the_number_of_returned_records()
    {
        $resultSet = [
            'id' => '1',
            'attribute1' => 'value1',
            'attribute2' => 'same',
        ];

        $this->collection->shouldReceive('toArray')->once()
            ->andReturn($resultSet);
        $this->builder->shouldReceive('limit')->with(1)->once()
            ->andReturn($this->builder);
        $this->builder->shouldReceive('get')->once()
            ->andReturn($this->collection);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $result = $this->limit(1)->getAll();
        $result->shouldHaveType('Illuminate\Database\Eloquent\Collection');
        $result->toArray()->shouldEqual($resultSet);
    }

    function it_applies_a_criterion_to_sort_the_returned_records()
    {
        $resultSet = [
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

        $this->collection->shouldReceive('toArray')->once()
            ->andReturn($resultSet);
        $this->builder->shouldReceive('orderBy')->with('attribute2', 'asc')
            ->once()->andReturn($this->builder);
        $this->builder->shouldReceive('get')->once()
            ->andReturn($this->collection);
        $this->model->shouldReceive('newQuery')->once()
            ->andReturn($this->builder);

        $result = $this->orderBy('attribute2')->getAll();
        $result->shouldHaveType('Illuminate\Database\Eloquent\Collection');
        $result->toArray()->shouldEqual($resultSet);
    }
}
