# Simple and small MVC

This mvc is intended for small-scale projects, and I'm trying to make it similar to Laravel)

Used PHP version is 8.1.

## Creating Model

When you created model you have to override table property.
```php
<?php

namespace App\Models;

use Src\Facades\Model;

class User extends Model
{
    protected string $table = 'users';
}
```

## Creating Controller
In controller you can route your data's to view.
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index($name, $age)
    {
        $users = User::where('name', '=', $name)->where('age', '=', $age)->paginate();

        return view('users', compact('users'));
    }
}
```

## Routing

```php
# GET route. (if you are not passing method name, it will be by default GET)
\Src\Facades\Route::run('/user', 'UserController@index', 'get');

# Post route.
\Src\Facades\Route::run('/user/store', 'UserController@store', 'post');

# Using callback
\Src\Facades\Route::run('/user', function () {
    echo 'Hello World!.';
})
```

## HTTP Request
```php
# Get all requests.
\Src\Facades\Request::all();

# Get request method.
\Src\Facades\Request::method();

# Get get method by key.
\Src\Facades\Request::get('key');

# Get pos method by key.
\Src\Facades\Request::post('key');
```

## Validation
```php
# Usage

    public function store()
    {
        $request = Request::all();

        $rules = Validation::make($request, [
            'name' => 'string|required',
            'email' => 'email',
            'password' => 'string|required'
        ]);

        User::create($rules);
    }
```

## Session
```php
# Get Session by key.
\Src\Facades\Session::get('key');

# Set Session.
\Src\Facades\Session::set('key', ['data' => 'test']);

# Remove Session by key.
\Src\Facades\Session::remove('key');

# Clear all sessions.
\Src\Facades\Session::clear();
```

## Service Provider

You can create your own Service Provider and use it. Just create your service provider under app/Providers folder and register it under config/app file.

Service Provider example.
```php
<?php

namespace App\Providers;

use Bootstrap\Provider;
use Src\Facades\Session;

class SessionServiceProvider extends Provider
{
    public static function boot()
    {
        Session::start();
    }
    
}
```

Register your Service Provider in config/app.php.
```php
<?php

return [

    'providers' => [
        App\Providers\AppServiceProvider::class,
        App\Providers\SessionServiceProvider::class,
        App\Providers\RequestServiceProvider::class,
        App\Providers\DotEnvServiceProvider::class,
    ]
];
```

All Service Providers booting in main Provider class.
```php
<?php

namespace Bootstrap;

class Provider
{
    public static function run()
    {
        self::getProviders();
    }

    private static function getProviders()
    {
        $app = include_once '../config/app.php';

        foreach ($app['providers'] as $provider) {
            $provider::boot();
        }
    }

}
```

## Environment Variables
Added dotenv package for environment variables. You can find more details in this package.

```
composer require vlucas/phpdotenv
```

## Debug

Added Symfony var dumper. You can find more details in this package.

##### dd('test');

```
composer require symfony/var-dumper
```

## Available ORM functionality

| Joins     | Queries      | CRUD     |
|-----------|--------------|----------|
| join()      | first()      | create() |
| innerJoin() | get()        | update() |
| leftJoin()  | findById()   | delete() |
| rightJoin() | where()      |          |
| fullJoin() | orWhere()    |          |
| fullOuterJoin() | whereIn()    |          |
| crossJoin() | orWhereIn()  |          |
|  | whereNotIn() |          |
|  | select()     |          |
|  | groypBy()    |          |
|  | having()     |          |
|  | limit()      |          |
|  | paginate()   |          |
|  | count()      |          |

## Simple usages
```php
# Join 
User::select(['name', 'COUNT(user_id) as count'])
->join('posts', 'posts.user_id', '=', 'users.id')
->get();

# Multiple joins
User::select(['name', 'COUNT(user_id) as count'])
->join('posts', 'posts.user_id', '=', 'users.id')
->leftJoin('pages', 'posts.page_id', '=', 'pages.id')
->get();

# GroupBy
User::select(['name', 'COUNT(user_id) as count'])
->join('posts', 'posts.user_id', '=', 'users.id')
->groupBy('name')
->get();

# Having
User::select(['name', 'COUNT(user_id) as count'])
->join('posts', 'posts.user_id', '=', 'users.id')
->groupBy('name')
->having('age' > 18)
->get();

# Paginate by default is 10, but you can change it.
User::where('name', '=', 'Sarxan')->orWhereIn('id', [1,2,3,4])->paginate(20)

# You can display all validation error messages or one by one under form input.

# Show all errors.
if (Session::has('errors')) {
    foreach (Session::get('errors') as $error) {
        echo $error;
    }
}

# Show under input

# Just pass your input name into error helper.

<input type="text" class="form-control" name="name" id="name" placeholder="Enter name">
 <?= error('name')?>

```

## Available validations

| Validations  |
|--------------|
| required     |
| string       |
| integer      |
| email        |
| alphanumeric |

### Usage validations
```php
  $request = Request::all();

  $rules = Validation::make($request, [
            'name' => 'string|required',
            'email' => 'email',
            'password' => 'string|required'
        ]);
```



## Upcoming features
New validation keywords and COOKIE class.
