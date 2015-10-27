<?php

namespace Monospice\SpicyRepositories\Laravel\Traits;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;

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
    /**
     * The relationships of the model used for the withRelated() criterion
     *
     * @var array
     */
    protected $related;

    // Inherit Doc from Interfaces\BasicCriteria
    public function onlyCriterion(QueryBuilder $query, $columns)
    {
        $columns = static::getArgumentsArray($columns, func_get_args());

        return $query->select($columns);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function excludeCriterion(QueryBuilder $query, $columns)
    {
        $columns = static::getArgumentsArray($columns, func_get_args());

        // Get the attributes of the model and filter out all but specified
        $schema = $query->getQuery()->getConnection()->getSchemaBuilder();
        $allColumns = $schema->getColumnListing($query->getModel()->getTable());
        $difference = array_diff($allColumns, $columns);

        return $query->select($difference);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function limitCriterion(QueryBuilder $query, $limit)
    {
        return $query->limit($limit);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function orderByCriterion(
        QueryBuilder $query,
        $column,
        $direction = 'asc'
    ) {
        return $query->orderBy($column, $direction);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function withCriterion(QueryBuilder $query, $related)
    {
        $related = static::getArgumentsArray($related, func_get_args());

        return $query->with($related);
    }

    // Inherit Doc from Interfaces\BasicCriteria
    public function withRelatedCriterion(QueryBuilder $query)
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

    /**
     * Get the array of dynamic arguments without the QueryBuilder argument
     *
     * @param mixed $array     Might be an array or the first passed argument
     * @param array $arguments The arguments passed to the method
     *
     * @return array The sanitized array of arguments
     */
    protected function getArgumentsArray($array, array $arguments)
    {
        if (is_array($array)) {
            return $array;
        }

        array_shift($arguments);

        return $arguments;
    }
}
