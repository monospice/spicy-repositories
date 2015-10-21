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
    public function onlyCriterion($query, $columns)
    {
        if (! is_array($columns)) {
            $columns = func_get_args();
            array_shift($columns);
        }

        return $query->select($columns);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function excludeCriterion($query, $columns)
    {
        if (! is_array($columns)) {
            $columns = func_get_args();
            array_shift($columns);
        }

        $schemaBuilder = $query->getQuery()->getConnection()->getSchemaBuilder();
        $all = $schemaBuilder->getColumnListing($query->getModel()->getTable());
        $difference = array_diff($all, $columns);

        return $query->select($difference);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function limitCriterion($query, $limit)
    {
        return $query->limit($limit);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function orderByCriterion($query, $column, $direction = 'asc')
    {
        return $query->orderBy($column, $direction);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function withCriterion($query, $related)
    {
        if (! is_array($related)) {
            $related = func_get_args();
            array_shift($related);
        }

        return $query->with($related);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function withRelatedCriterion($query)
    {
        if ($this->related === null) {
            throw new \RuntimeException(
                'No relationships defined in the repository. Eloquent does ' .
                'not support reading relationships from the Model. Related ' .
                'models must be defined in the $related property of the ' .
                'repository.'
            );
        }

        return $query->with($this->related);
    }
}
