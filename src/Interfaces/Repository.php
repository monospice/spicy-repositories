<?php

namespace Monospice\SpicyRepositories\Interfaces;

/**
 * Defines methods for Repository classes
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
interface Repository
{
    /**
     * Get all of the records for the current model
     *
     * @return \Illuminate\Database\Eloquent\Collection The result set
     */
    public function getAll();

    /**
     * Get a paginated set of records for the current model
     *
     * @param int    $perPage  The number of records to show on each page
     * @param string $pageName The identifier of the URL parameter to use for
     * tracking pagination
     * @param int    $page     The page number to get records for
     *
     * @return \Illuminate\Pagination\Paginator The paginated result set
     *
     * @throws \InvalidArgumentException If the requested pagination is invalid
     */
    public function paginateAll(
        $perPage = null,
        $pageName = 'page',
        $page = null
    );

    /**
     * Get a record by the ID
     *
     * @param int $id The id of the record to return
     *
     * @return \Illuminate\Database\Eloquent\Collection The result set
     */
    public function get($id);

    /**
     * Get a set of records where a column matches the specified value
     *
     * @param string $column The column used to match records
     * @param mixed  $record The value of the record to match on the column
     *
     * @return \Illuminate\Database\Eloquent\Collection The result set
     */
    public function getBy($column, $record);

    /**
     * Get a paginated set of records for the current model where a column
     * matches the specified value
     *
     * @param string $column   The column used to match records
     * @param mixed  $record   The value of the record to match on the column
     * @param int    $perPage  The number of records to show on each page
     * @param string $pageName The identifier of the URL parameter to use for
     * tracking pagination
     * @param int    $page     The page number to get records for
     *
     * @return \Illuminate\Pagination\Paginator The paginated result set
     *
     * @throws \InvalidArgumentException If the requested pagination is invalid
     */
    public function paginateBy(
        $column,
        $record,
        $perPage = null,
        $pageName = 'page',
        $page = null
    );

    /**
     * Get a list as an associative array created from the data set
     *
     * @param string $valueColumn The column used for the value of the array
     * @param string $keyColumn   The column used for the key of the array
     *
     * @return array The list of records as an associative array
     *
     * @throws \RuntimeException If the method cannot convert the data into an
     * array
     */
    public function getList($valueColumn, $keyColumn = 'id');

    /**
     * Get the result of the last create, update, or delete query
     *
     * @return mixed The value of the result of the last create, update, or
     * delete query
     */
    public function getResult();

    /**
     * Set the result of the last create, update, or delete query. Used by
     * repository operation classes to pass the result back to the repository
     * instance
     *
     * @param mixed $result The value of the last create, update, or delete
     * query
     *
     * @return $this The current repository instance for method chaining
     */
    public function setResult($result);

    /**
     * Create a record using an array of data, usually input from a form
     *
     * @param array $data The data used to create the record
     *
     * @return $this The current repository instance for method chaining
     */
    public function create(array $data);

    /**
     * Update a record using an array  of data, usually input from a form
     *
     * @param mixed  $record The value of the column used to match the updated
     * record
     * @param array  $data   The data used to update the record
     * @param string $column The column used to match the updated record
     *
     * @return $this The current repository instance for method chaining
     */
    public function update($record, array $data, $column = 'id');

    /**
     * Update a record or create a new record if it doesn't exist
     *
     * @return \Monospice\SpicyRepositories\Interfaces\UpdateOrCreate The
     * instance of the class that performs the update or create operation
     */
    public function updateOrCreate();

    /**
     * Delete a record
     *
     * @param mixed  $record The value of the column used to match the deleted
     * record
     * @param string $column The column used to match the deleted record
     *
     * @return $this The current repository instance for method chaining
     */
    public function delete($record, $column = 'id');
}
