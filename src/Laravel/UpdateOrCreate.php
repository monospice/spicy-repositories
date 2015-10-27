<?php

namespace Monospice\SpicyRepositories\Laravel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use Monospice\SpicyRepositories\Interfaces;

/**
 * Performs an update operation or a create operation of the record doesn't
 * exist
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
class UpdateOrCreate implements Interfaces\UpdateOrCreate
{
    /**
     * The original instance of the repository performing the updateOrCreate
     * operation
     *
     * @var Interfaces\Repository
     */
    protected $repository;

    /**
     * The instance of the model used to perform the updateOrCreate operation
     *
     * @var Model
     */
    protected $model;

    /**
     * An associative array of column => value pairs used to match records for
     * update or set for create
     *
     * @var array
     */
    protected $where;

    /**
     * An associative array of column => value pairs to set on the record
     *
     * @var array
     */
    protected $set;

    /**
     * Create a new instance of this class
     *
     * @param Interfaces\Repository $repository The original instance of the
     * repository performing the updateOrCreate operation
     * @param Model                 $model      The instance of the model used
     * to perform the updateOrCreate operation
     */
    public function __construct(Interfaces\Repository $repository, Model $model)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    // Inherit Doc from Interfaces\UpdateOrCreate
    public function where(array $where)
    {
        $this->where[] = $where;

        return $this;
    }

    // Inherit Doc from Interfaces\UpdateOrCreate
    public function orWhere(array $where)
    {
        return $this->where($where);
    }

    // Inherit Doc from Interfaces\UpdateOrCreate
    public function set(array $set)
    {
        $this->set = $set;

        if ($this->where === null || count($this->where) === 0) {
            throw new \RuntimeException(
                'No where clause specified. Please set at least one condition.'
            );
        }

        return $this->execute();
    }

    /**
     * Execute the updateOrCreate operation
     *
     * @return Interfaces\Repository The original instance of the repository
     * performing the updateOrCreate operation for method chaining
     */
    protected function execute()
    {
        if (count($this->where === 1)) {
            $model = $this->performUpdateOrCreate($this->where[0]);
            $this->repository->setResult($model);

            return $this->repository;
        }

        $collection = new Collection();

        foreach ($this->where as $where) {
            $collection->add($this->performUpdateOrCreate($where));
        }

        $this->repository->setResult($collection);

        return $this->repository;
    }

    /**
     * Perform an updateOrCreate operation for a where clause
     *
     * @param array $where An associative array of column => value pairs used
     * to match records for update or set for create
     *
     * @return \Illuminate\Database\Eloquent\Model The updated or created model
     */
    protected function performUpdateOrCreate(array $where)
    {
        return $this->model->updateOrCreate($where, $this->set);
    }
}
