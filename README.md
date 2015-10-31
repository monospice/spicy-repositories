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

### Install the package:

```
$ composer require monospice/spicy-repositories
```

### In a Laravel application:

First, install the service provider to autoload repositories by creating
a Repository Service Provider in your app:

```php
use Monospice\SpicyRepositories\Laravel\EloquentRepositoryServiceProvider;

class RepositoryServiceProvider extends EloquentRepositoryServiceProvider
{
    // repository binding methods here
}
```

The Service Provider above binds your repositories into the Laravel container
so Laravel will automagically inject an instance of your repository into any
controllers that typehint the repository's interface.

To instruct the package to bind a repository class, add a method to the service
provider you just created that defines the abstract repository interface and
returns the class name of the matching concrete repository:

```php
class RepositoryServiceProvider extends EloquentRepositoryServiceProvider
{
    protected function bindUserRepository()
    {
        $this->interface = \App\Repositories\Interfaces\UserRepository::class;

        return \App\Repositories\UserRepository::class;
    }
}
```

In some cases, a developer may need additional functionality to instantiate
a repository. The repository binding method may also return an anonymous
function with the new repository instance:

```php
class RepositoryServiceProvider extends EloquentRepositoryServiceProvider
{
    protected function bindUserRepository()
    {
        $this->interface = \App\Repositories\Interfaces\UserRepository::class;

        return function () {
            if ($someCondition) {
                $model = new \App\User();
            } else {
                $model = new \App\AdminUser();
            }

            return new \App\Repositories\UserRepository($model);
        };
    }
}
```

Repository binding methods must begin with `bind` and end with `Repository`.
This naming convention encourages readable definitions of the repository
class bindings.

The EloquentRepositoryServiceProvider class calls the register method for you,
so there's no need to redefine it here.

Be sure to add the new Service Provider to the services array in your
`config/app.php` file:

```php
...
    // Laravel >= 5.1:
    App\Providers\RepositoryServiceProvider::class,
    // Laravel < 5.1:
    'App\Providers\RepositoryServiceProvider',
...
```

Creating Repositories
---------------------

Create a new repository and it's interface by extending the package's classes.

**For the repository interface:**

```php
use Monospice\SpicyRepositories\Interfaces\Repository;
use Monospice\SpicyRepositories\Interfaces\BasicCriteria;

interface UserRepositoryInterface extends Repository, BasicCriteria
{
    // custom repository methods here
}
```

**For the concrete repository class:**

Ensure that the repository class receives an instance of the model in the
constructor. If using the service provider described in the previous section,
the framework will automatically inject an instance of the model.

```php
use Monospice\SpicyRepositories\Laravel\EloquentRepository;
use App\Repositories\UserRepositoryInterface;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    // custom repository methods here
}
```

Alternatively, you may choose to create a base repository class and interface
for your application so you only need to extend the package classes once.

Now, this repository can be used in controllers by type-hinting the repository's
interface. Laravel will inject an instance automatically:

```php
class UserController extends Controller
{
    public function __construct(UserRepositoryInterface $users)
    {
        // use the repository
        $users->getAll();
    }
}
```

Repository Methods
------------------

Repositories shine when you define your own custom reusable methods for
specific cases. For convenience, however, the repositories in this package come
with the following generic methods:

### Retrieving Data

**getAll()** - retrieve all records of a model

```php
$repository->getAll();
```

**paginateAll()** - paginate a set of all records of a model

```php
$itemsPerPage = 20;
$repository->paginateAll($itemsPerPage);
```

**getFirst()** - retrieve the first record in a set

```php
$repository->getFirst();
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

**listAll()** - retrieve an array of all records containing the

values of one attribute

```php
$column = 'name';
$repository->listAll($column);
```

**exists()** - determine if any records exist

```php
$repository->exists();
```

This method is more useful for checking if records exist after applying
criteria to a query:

```php
$repository->forUserType($userType)->exists();
```

**count()** - retrieve the number of records for a query

```php
$repository->count();
```

With no criteria, this method returns the number of all records for a model.
`count()` is more useful for determining the number of records after applying
criteria:

```php
$repository->forUserType($userType)->count();
```

For more information about repository criteria, see the **Criteria** section
later in this document.

### Modifying Data

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

**updateOrCreate()** - update an existing record or create it if it doesn't
exist

```php
$repository->updateOrCreate()
    ->where([
        'first' => 'George',
        'last' => 'Washington',
    ])
    ->set([
        'occupation' => 'President of the US'
    ]);
```

In the example above, the repository will set the `occupation` field of the
record if the record exists. Otherwise, it will create a new record and set
all three fields to the given values.

One may specify multiple where clauses to find records by and the operation
will update each matching record or create a new record:

```php
$repository->updateOrCreate()
    ->where(['first' => 'George'])
    ->orWhere(['first' => 'Denzel'])
    ->set(['occupation' => 'Some guy named Washington']);
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

### Custom Methods

Define custom methods in the repository classes that extend this package's
base classes.

For example:

```php
class UserRepository extends EloquentRepository implements UserRepositoryInterface
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

**with()** - eager load (in same query) the specified relationships (defined
on the model)

```php
$repository->with('comments')->getAll();
```

**withRelated()** - eager load (in the same query) all the relationships defined on
the model and in the repository (these relationships must be defined manually
in the repository--there is no way to gather them from an Eloquent model at
this time)

```php
$repository->withRelated()->getAll();
```

And in the repository, add this property:

```php
protected $related = ['likes', 'comments'];
```

### Where's *where()*?

To encourage the creation of reusable criteria, this repository framework
explicitly excludes a `where()` criterion from the package. The framework
intends for developers to compose readable criteria that perform the same
functionality as *where* clauses instead of building complex queries each
time that functionality is needed.

If a project really needs a *where* criterion, one may define a criterion
method in their child repository class like the following:

```php
public function whereCriterion($query, $column, $boolean, $value)
{
    return $query->where($column, $boolean, $value);
}
```

For more information, see the next subsection.

### Custom Criteria

Repositories are especially powerful when developers create custom, reusable
criteria for their repositories. These criteria should abstract units of complex
or frequently used logic.

For example, a developer may create an `honorStudents()` criteria that filters
results by user type and grade average, and a `freshman()` criteria that
filters by user type and grade level.

Combined, these criteria instruct the repository to return all honors freshmen.
The implementation never needs to know about the inner workings of the data
layer:

```php
$repository->freshman()->honorStudents()->getAll(); // freshman honors students
$repository->honorStudents()->getAll();             // all honors students
```

To create custom criteria, define methods in the repository (and its interface)
that end with `Criterion` or `Criteria`:

```php
public function honorStudentCriterion($query, $gradeThreshold = 90)
{
    return $query
        ->where('user_type', 'student')
        ->where('grade_average', '>=', $gradeThreshold);
}
```

In the example above, calls to `honorStudents()` automatically invoke the
`honorStudentsCriterion()` method and pass the $query parameter along with any
other parameters supplied to the `honorStudents()` method call.

This convention encourages readable definitions of repository criteria in
repository classes. Note that one should not declare the `honorStudents()`
method explicitly. The repository framework handles the dynamic method call.

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
    ->withRelated()
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
