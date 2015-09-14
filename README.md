Spicy Repositories
=======

[![Build Status](https://travis-ci.org/monospice/spicy-identifier-tools.svg?branch=master)](https://travis-ci.org/monospice/spicy-identifier-tools)

**A lightweight repository framework using functional criteria for more fluid
code.**

Repositories stand between an application's business logic and it's database
manipulation layer. This abstraction speeds development and improves
application maintainability by making data access more consistent and flexible.

This repository implementation provides built-in support for the Laravel
Eloquent ORM in Laravel versions 4 and 5.

Simple Example
------

```php
$users = new UserRepository(new UserModel());

$users->getAll();
$users->getBy('age', '21');
$users->only('name', 'age')->get($id);
$users->filterByCustomCriteria()->orderBy('name')->getAll();
```

Installation
-------

**Install the package:**

```
$ composer require monospice/spicy-repositories
```

**In a Laravel application:**

First, install the service provider to autoload repositories by creating
a Repository Service Provider in your app:

```php
use Monospice\SpicyRepositories\Laravel\EloquentRepositoryServiceProvider;

class RepositoryServiceProvider extends EloquentRepositoryServiceProvider
{
    protected function repositories()
    {
        return [
            'Namespace\Of\Your\Repository\Interface', function($app) {
                // any setup logic
                $model = new YourModel();
                return new Namespace\Of\Your\ConcreteRepository($model);
            },
            // For example:
            'App\Repositories\UserRepositoryInterface', function($app) {
                return new App\Repositories\UserRepository(new App\User());
            },
            'App\Repositories\PostRepositoryInterface', function($app) {
                return new App\Repositories\PostRepository(new App\Post());
            },
        ];
    }
}
```

The Service Provider above binds your repositories into the Laravel container
so Laravel will automagically inject an instance of your repository into any
controllers that typehint the repository's interface.

The EloquentRepositoryServiceProvider class calls the register method for you,
so there's no need to redefine it here.

Be sure to add the new Service Provider to your app.config:

```php
...
    // Laravel >= 5.1:
    App\Providers\RepositoryServiceProvider::class,
    // Laravel < 5.1:
    'App\Providers\RepositoryServiceProvider',
...
```

Next, create a new repository and it's interface by extending the following
classes:

```php
use Monospice\SpicyRepositories\Interfaces\Repository;
use Monospice\SpicyRepositories\Interfaces\BasicCriteria;

interface UserRepositoryInterface extends Repository, BasicCriteria
{
    // custom repository methods here
}
```
```php
use Monospice\SpicyRepositories\Laravel\EloquentRepository;
use App\Repositories\UserRepositoryInterface;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::construct($user);
    }

    // custom repository methods here
}
```

Alternatively, you may choose to create a base repository class and inteface
for your application so you only need to extend the package classes once.

Now this repository can be used in controllers by type-hinting the repository's
interface. Laravel will inject an instance automatically:

```php
class UserController extends Controller
{
    public function __construct(UserRepositoryInterface $repository)
    {
        // use the repository
        $repository->getAll();
    }
}
```

Repository Methods
------------------

Repositories shine when you define your own custom reusable methods for
specific cases. For convenience, however, the repositories in this package come
with the following generic methods:

**getAll()** - retrieve all records of a model

```php
$repository->getAll();
```

**paginateAll()** - paginate a set of all records of a model

```php
$itemsPerPage = 20;
$repository->paginateAll($itemsPerPage);
```

**get()** - retrieve a single record of a model by ID

```php
$recordId = 1;
$repository->get($recordId);
```

**getBy()** - retreive a set of records where an attribute equals the
specified value

```php
$column = 'name';
$value = 'George Washington';
$repository->getBy($column, $value);
```

**paginateBy()** - paginate a set of records where an attribute equals the
specified value

```php
$itemsPerPage = 20;
$column = 'name';
$value = 'George Washington';
$repository->paginateBy($column, $value, $itemsPerPage);
```

**listAll()** - retrieve an associative array of all records containing the

values of one attribute

```php
$column = 'name';
$repository->listAll($column);
```

**create()** - create a new record from an array of attribute data

```php
$input = ['first' => 'George', 'last' => 'Washington'];
$repository->create($input);
```

**update()** - update an existing record using an array of attribute data

```php
$recordId = 1;
$changes = ['first' => 'Denzel'];
$repository->update($recordId, $changes);
```

**delete()** - delete the specified record

```php
$recordId = 1;
$repository->delete($recordId);
```

**getResult()** - get the return value of the last create, update, or delete
operation

```php
$repository->getResult();
```

**Custom Methods**

Define custom methods in the repository classes that extend this package's
base classes.

For example:

```php
class UserRepository extends EloquentRepository
{
    public function customGetAll()
    {
        return $this->orderBy('name')->getAll();
    }
}

$repository->customGetAll(); // the custom method
$repository->getAll();       // a built-in method
```

Criteria
--------

Repository Criteria are reusable constraints that the repository applies to
a query. For more fluid code, this package uses functional criteria instead of
criteria classes.

Most criteria should be created for a specific application's requirements.
This package provides some basic criteria to get you started:

**only()** - retrieve only the specified columns in the result set

```php
$repository->only('name', 'email')->getAll();
```

**exclude()** - retrieve all but the specified columns in the result set

```php
$repository->exclude('password')->getAll();
```

**limit()** - retrieve no more than the specified number of records in the
result set


```php
$repository->limit(5)->getAll();
```

**orderBy()** - sort the returned result set by the specified column

```php
$repository->orderBy('name')->getAll();
$repository->orberBy('age', 'desc')->getAll();
```

**Custom Criteria**

Repositories are especially powerful when developers create custom, reusable
criteria for their repositories. These criteria should abstract units of complex
or frequently used logic.

For example, a developer may create an `honorStudent()` criteria that filters
results by user type and grade average, and a `freshman()` criteria that
filters by user type and grade level.

Combined, these criteria instruct the repository to return all honors freshmen.
The implementation never needs to know about the inner workings of the data
layer:

```php
$repository->freshman()->honorStudent()->getAll(); // freshman honors students
$repository->honorStudent()->getAll();             // all honors students
```

To create custom criteria, define methods in the repository (and its interface)
that call the `addCriterion()` method:

```php
public function honorStudent($gradeThreshold = 90)
{
    $this->addCriterion(function($query) use ($gradeThreshold) {
        return $query
            ->where('user_type', 'student')
            ->where('grade_average', '>=', $gradeThreshold);
    });

    return $this; // don't forget the return statement for method chaining
}
```

Optionally, increase the reusability of your criteria by defining them in
traits shared by repositories for models with the same attributes or
functionality.

Method Chaining
---------------

Methods that do not return an output value can be chained:

```php
$result = $repository
    ->create($new)
    ->delete($old)
    ->update($related)
    ->orderBy('name')
    ->exclude('password')
    ->someCustomCriteria()
    ->getAll();
```

Testing
-------

The Spicy Repositories package uses PHPUnit to perform functional tests using
the database with Eloquent, and it uses PHPSpec for object behavior.

``` bash
$ phpunit
$ vendor/bin/phpspec run
```

License
-------

The MIT License (MIT). Please see the [LICENSE File](LICENSE) for more
information.
