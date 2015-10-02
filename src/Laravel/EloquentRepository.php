<?php

namespace Monospice\SpicyRepositories\Laravel;

use Illuminate\Database\Eloquent\Model;

use Monospice\SpicyRepositories\AbstractRepository;
use Monospice\SpicyRepositories\Interfaces\BasicCriteria;
use Monospice\SpicyRepositories\Laravel\Traits;

/**
 * Defines methods for Repository classes
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
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Start a new query on the model, applying any existing criteria
     *
     * @override
     *
     * @return mixed The new query object
     */
    public function query()
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
        return $this->query()->get();
    }

    // Inherit Doc from Interfaces\Repository
    public function paginateAll(
        $perPage = null,
        $pageName = 'page',
        $page = null
    ) {
        return $this->query()->paginate($perPage, ['*'], $pageName, $page);
    }

    // Inherit Doc from Interfaces\Repository
    public function get($id)
    {
        return $this->query()->find($id);
    }

    // Inherit Doc from Interfaces\Repository
    public function getBy($column, $record)
    {
        return $this->query()->where($column, '=', $record)->get();
    }

    // Inherit Doc from Interfaces\Repository
    public function paginateBy(
        $column,
        $record,
        $perPage = null,
        $pageName = 'page',
        $page = null
    ) {
        return $this->query()->where($column, '=', $record)
            ->paginate($perPage, ['*'], $pageName, $page);
    }

    // Inherit Doc from Interfaces\Repository
    public function getList($valueColumn, $keyColumn = 'id')
    {
        $list = $this->query()->lists($valueColumn, $keyColumn);

        if (is_array($list)) {
            return $list;
        }

        if (get_class($list) === 'Illuminate\Support\Collection') {
            return $list->toArray();
        }

        throw new \RuntimeException(
            'Could not convert returned ' .gettype($list) . ' into an array.'
        );
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

        if ($query->count() === 0) {
            $this->result = false;
            return $this;
        } elseif ($query->count() === 1) {
            $this->result = $query->first()->update($data);
        } else {
            $this->result = true;

            $query->get()->each(function ($row) use ($data) {
                $result = $row->update($data);

                if (! $result) {
                    $this->result = false;
                }
            });
        }

        return $this;
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
