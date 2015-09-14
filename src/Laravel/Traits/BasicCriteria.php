<?php

namespace Monospice\SpicyRepositories\Laravel\Traits;

/**
 * Defines methods for adding basic criteria to Repository classes
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
trait BasicCriteria
{
    // Inherit Doc from Interfaces\BasicCriteria
    public function only($columns)
    {
        if (! is_array($columns)) {
            $columns = func_get_args($columns);
        }

        $this->addCriteria(function($query) use ($columns) {
            return $query->select($columns);
        });

        return $this;
    }

    public function exclude($columns)
    {
        if (! is_array($columns)) {
            $columns = func_get_args($columns);
        }

        $this->addCriteria(function($query) use ($columns) {
            $schemaBuilder = $query->getQuery()->getConnection()->getSchemaBuilder();
            $all = $schemaBuilder->getColumnListing($query->getModel()->getTable());
            $difference = array_diff($all, $columns);

            return $query->select($difference);
        });

        return $this;
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function limit($limit)
    {
        $this->addCriteria(function($query) use ($limit) {
            return $query->limit($limit);
        });

        return $this;
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function orderBy($column, $direction = 'asc')
    {
        $this->addCriteria(function($query) use ($column, $direction) {
            return $query->orderBy($column, $direction);
        });

        return $this;
    }
}
