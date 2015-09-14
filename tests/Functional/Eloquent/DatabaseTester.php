<?php

namespace Monospice\SpicyRepositories\Test\Functional\Eloquent;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Sets up a database connection for testing Eloquent models
 *
 * @category Package
 * @package  Monospice\SpicyRepositories
 * @author   Cy Rossignol <cy@rossignols.me>
 * @license  See LICENSE file
 * @link     http://github.com/monospice/spicy-repositories
 */
class DatabaseTester
{
    /**
     * The Eloquent Database Manager Capsule
     *
     * @var Illuminate\Database\Capsule\Manager The instance of the Eloquent
     * Capsule Manager
     */
    protected $capsule;

    /**
     * The Eloquent database driver to use
     *
     * @var string
     */
    protected $driver;

    /**
     * The database name to use
     *
     * @var string
     */
    protected $database;

    /**
     * The prefix to use
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new instance of this class and initialize the database
     * connection
     *
     * @param array $connection The array of connection configuration options
     */
    public function __construct(array $connection = null)
    {
        $this->capsule = new Capsule();

        $this->capsule->getContainer()->bind(
            'paginator',
            'Illuminate\Pagination\Paginator'
        );

        if ($connection !== null) {
            $this->configureConnection($connection);
            $this->connect();
        }
    }

    /**
     * Set the database connection to use
     *
     * @param array $connection The array of connection configuration options
     *
     * @return TestDatabase The instance of TestDatabase for method chaining
     */
    public function configureConnection(array $connection)
    {
        $this->setDriver($connection['driver']);
        $this->setDatabase($connection['database']);
        $this->setPrefix($connection['prefix']);

        return $this;
    }

    /**
     * Set the database driver to use
     *
     * @param string $driver The database driver to use
     *
     * @return TestDatabase The instance of TestDatabase for method chaining
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Set the database name to use
     *
     * @param string $database The database name to use
     *
     * @return TestDatabase The instance of TestDatabase for method chaining
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * Set the database prefix to use
     *
     * @param string $prefix The database prefix to use
     *
     * @return TestDatabase The instance of TestDatabase for method chaining
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Set up Eloquent and connect to the database
     *
     * @return TestDatabase The instance of TestDatabase for method chaining
     */
    public function connect()
    {
        $this->capsule->addConnection([
            'driver' => $this->driver,
            'database' => $this->database,
            'prefix' => $this->prefix,
        ], 'default');

        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        return $this;
    }

    /**
     * Create a new table in the test database
     *
     * @param string   $name   The name of the new table
     * @param function $schema The closure that defines the schema of the table
     *
     * @return TestDatabase The instance of TestDatabase for method chaining
     */
    public function createTable($name, $schema)
    {
        $this->capsule->schema()->dropIfExists($name);
        $this->capsule->schema()->create($name, $schema);

        return $this;
    }

    /**
     * Insert records into the test database
     *
     * @param string $table The table to insert data into
     * @param array  $data  The array of data to insert
     *
     * @return TestDatabase The instance of TestDatabase for method chaining
     */
    public function seed($table, array $data)
    {
        $this->capsule->table($table)->insert($data);
    }

    /**
     * Assert that the specified data exists in the table
     *
     * @param string $table The table to check
     * @param array  $data  The data to check for
     *
     * @return bool True if the data exists in the table
     */
    public function seeIn($table, array $data)
    {
        \PHPUnit_Framework_Assert::assertTrue($this->exists($table, $data));
    }

    /**
     * Assert that the specified data doesn't exist in the table
     *
     * @param string $table The table to check
     * @param array  $data  The data to check for
     *
     * @return bool True if the data exists in the table
     */
    public function dontSeeIn($table, array $data)
    {
        \PHPUnit_Framework_Assert::assertTrue(! $this->exists($table, $data));
    }

    /**
     * Check that the specified data exists in the table
     *
     * @param string $table The table to check
     * @param array  $data  The data to check for
     *
     * @return bool True if the data exists in the table
     */
    public function exists($table, array $data)
    {
        return $this->capsule->table($table)->where($data)->exists();
    }
}
