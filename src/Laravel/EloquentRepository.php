<?php

namespace Monospice\SpicyRepositories\Laravel;

use Illuminate\Database\Eloquent\Model;

use Monospice\SpicyRepositories\AbstractRepository;
use Monospice\SpicyRepositories\Interfaces\BasicCriteria;
use Monospice\SpicyRepositories\Laravel\Traits;

/**
 * An abstraction to access and manipulate data in Laravel using the repository
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
class EloquentRepository extends AbstractRepository implements BasicCriteria
{
    use Traits\HasCriteria;
    use Traits\BasicCriteria;

    /**
     * The model this repository provides access for
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new instance of this repository
     *
     * @param \Illuminate\Database\Eloquent\Model $model The model this
     * repository provides access for
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Inherit Doc from AbstractRepository
    protected function select()
    {
        $query = $this->model->newQuery();

        if (property_exists($this, 'criteria')) {
            $query = $this->applyCriteria($query);
        }

        return $query;
    }

    // Inherit Doc from Interfaces\Repository
    public function getAll()
    {
        return $this->select()->get();
    }

    // Inherit Doc from Interfaces\Repository
    public function getFirst()
    {
        return $this->select()->first();
    }

    // Inherit Doc from Interfaces\Repository
    public function paginateAll(
        $perPage = null,
        $pageName = 'page',
        $page = null
    ) {
        return $this->select()->paginate($perPage, ['*'], $pageName, $page);
    }

    // Inherit Doc from Interfaces\Repository
    public function get($id)
    {
        return $this->select()->find($id);
    }

    // Inherit Doc from Interfaces\Repository
    public function getBy($column, $record)
    {
        return $this->select()->where($column, '=', $record)->get();
    }

    // Inherit Doc from Interfaces\Repository
    public function paginateBy(
        $column,
        $record,
        $perPage = null,
        $pageName = 'page',
        $page = null
    ) {
        return $this->select()->where($column, '=', $record)
            ->paginate($perPage, ['*'], $pageName, $page);
    }

    // Inherit Doc from Interfaces\Repository
    public function getList($valueColumn, $keyColumn = 'id')
    {
        $list = $this->select()->lists($valueColumn, $keyColumn);

        if (is_array($list)) {
            return $list;
        }

        if (get_class($list) === 'Illuminate\Support\Collection') {
            return $list->toArray();
        }

        throw new \RuntimeException(
            'Could not convert returned [' . gettype($list) . '] into an array.'
        );
    }

    // Inherit Doc from Interfaces\Repository
    public function exists()
    {
        return $this->select()->exists();
    }

    // Inherit Doc from Interfaces\Repository
    public function count($columns = null)
    {
        // Count rows using the model's primary key by default
        if ($columns === null) {
            $columns = $this->model->getKeyName();
        }

        // If we cannot determine the columns to match, count all columns
        if (! $columns) {
            $columns = '*';
        }

        return $this->select()->count($columns);
    }

    // Inherit Doc from Interfaces\Repository
    public function create(array $data)
    {
        $this->result = $this->model->create($data);

        return $this;
    }

    // Inherit Doc from Interfaces\Repository
    public function update($record, array $data, $column = 'id')
    {
        $query = $this->model->where($column, '=', $record);
        $numRows = $query->count();

        if ($numRows === 0) {
            $this->result = null;

            return $this;
        }

        if ($numRows === 1) {
            $row = $query->first();
            $row->update($data);
            $this->result = $row;

            return $this;
        }

        $this->result = $query->get()->each(function ($row) use ($data) {
            $row->update($data);
        });

        return $this;
    }

    // Inherit Doc from Interfaces\Repository
    public function updateOrCreate()
    {
        return new UpdateOrCreate($this, $this->model);
    }

    // Inherit Doc from Interfaces\Repository
    public function delete($record, $column = 'id')
    {
        $this->result = $this->model->newQuery()
            ->where($column, '=', $record)
            ->delete();

        return $this;
    }
}
