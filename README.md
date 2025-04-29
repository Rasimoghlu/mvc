# Laravel-style MVC Framework

A lightweight MVC framework for PHP 8.1+ inspired by Laravel. Perfect for small to medium-scale projects.

## Installation

```bash
composer create-project rasimoghlu/mvc your-project-name
cd your-project-name
```

## Configuration

1. Copy `.env.example` to `.env` and configure your environment variables
2. Run your application with a PHP server:

```bash
cd public
php -S localhost:8000
```

## Key Features

- Simple and intuitive routing
- MVC architecture
- Database ORM with advanced query capabilities
- Middleware support
- Service providers
- Environment variable configuration
- CSRF protection
- Validation system
- Improved error handling

## Creating Models

Models provide an elegant way to interact with your database. Each model represents a table in your database.

```php
<?php

namespace App\Models;

use Src\Facades\Model;

class User extends Model
{
    protected string $table = 'users';
    
    // Define fillable fields (fields that can be mass-assigned)
    protected array $fillable = ['name', 'email', 'password'];
}
```

## Creating Controllers

Controllers handle incoming HTTP requests and return responses.

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;

class UserController
{
    public function index()
    {
        return view('users.index', [
            'users' => User::all()
        ]);
    }
    
    public function show(int $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return view('errors.404', [
                'message' => 'User not found'
            ]);
        }
        
        return view('users.show', [
            'user' => $user
        ]);
    }
    
    public function store()
    {
        try {
            $user = User::create([
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT)
            ]);
            
            return redirect("/user/{$user->id}");
        } catch (Exception $e) {
            return view('users.create', [
                'error' => 'Failed to create user'
            ]);
        }
    }
}
```

## Routing

Define your routes in the `route/web.php` file:

```php
<?php

use Src\Facades\Router;

// Basic routes
Router::run('/test', 'UserController@index', 'get');
Router::run('/test/store', 'UserController@store', 'post');

// Routes with parameters
Router::run('/user/{id}', 'UserController@show', 'get');
Router::run('/user/{id}/edit', 'UserController@edit', 'get');
Router::run('/user/{id}', 'UserController@update', 'put');
Router::run('/user/{id}', 'UserController@destroy', 'delete');

// Using callbacks
Router::run('/hello', function() {
    echo 'Hello World!';
});

// Apply middleware to a route
Router::middleware('/user/{id}', 'get', 'auth');
```

## HTTP Request

Access request data in your controllers:

```php
// Get all request data
$data = request();

// Get a specific request value
$name = request('name');

// Get request method
$method = \Src\Facades\Request::method();

// Get query parameter
$query = \Src\Facades\Request::get('key');

// Get POST data
$post = \Src\Facades\Request::post('key');
```

## Validation

Validate incoming request data:

```php
public function store()
{
    $request = \Src\Facades\Request::all();

    $rules = \Src\Facades\Validation::make($request, [
        'name' => 'string|required',
        'email' => 'email|required',
        'password' => 'string|required|min:8'
    ]);

    User::create($rules);
}
```

## Session Management

Work with sessions:

```php
// Get a session value
$value = \Src\Facades\Session::get('key');

// Set a session value
\Src\Facades\Session::set('key', ['data' => 'test']);

// Remove a session value
\Src\Facades\Session::remove('key');

// Clear all sessions
\Src\Facades\Session::clear();

// Generate CSRF token
$token = _token();

// Include CSRF field in forms
<?= csrf_field() ?>
```

## Service Providers

Create custom service providers to bootstrap application components:

```php
<?php

namespace App\Providers;

use Bootstrap\Provider;

class AppServiceProvider extends Provider
{
    public static function boot(): void
    {
        // Register error handler
        \App\Http\Exceptions\Whoops::handle();
        
        // Set application timezone
        date_default_timezone_set('UTC');
    }
}
```

Register providers in `config/app.php`:

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

## Database ORM Features

| Joins         | Queries       | CRUD     |
|---------------|---------------|----------|
| join()        | first()       | create() |
| innerJoin()   | get()         | update() |
| leftJoin()    | findById()    | delete() |
| rightJoin()   | where()       |          |
| fullJoin()    | orWhere()     |          |
| fullOuterJoin() | whereIn()     |          |
| crossJoin()   | orWhereIn()   |          |
|               | whereNotIn()  |          |
|               | select()      |          |
|               | groupBy()     |          |
|               | having()      |          |
|               | limit()       |          |
|               | paginate()    |          |
|               | count()       |          |

## Query Examples

```php
// Join example
User::select(['name', 'COUNT(user_id) as count'])
    ->join('posts', 'posts.user_id', '=', 'users.id')
    ->get();

// Multiple joins
User::select(['name', 'COUNT(user_id) as count'])
    ->join('posts', 'posts.user_id', '=', 'users.id')
    ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
    ->get();

// Group by with having
User::select(['name', 'COUNT(user_id) as count'])
    ->join('posts', 'posts.user_id', '=', 'users.id')
    ->groupBy('name')
    ->having('count', '>', 5)
    ->get();

// Pagination
$users = User::where('active', '=', 1)
    ->orWhereIn('role', ['admin', 'editor'])
    ->paginate(20);
```

## Form Validation

Display validation errors in your views:

```php
// Show all errors
<?php if (isset($_SESSION['errors'])): ?>
    <div class="alert alert-danger">
        <?php foreach ($_SESSION['errors'] as $field => $error): ?>
            <p><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

// Show specific field error
<input type="text" name="email" class="form-control" value="<?= old('email') ?>">
<?php if ($error = error('email')): ?>
    <div class="text-danger"><?= $error ?></div>
<?php endif; ?>
```

## Helper Functions

```php
// Dump and die (debug)
dd($variable);

// Escape HTML
echo e($unsafeString);

// Sanitize user input
$clean = sanitize_input($_POST['input']);

// Clean HTML content
$safeHtml = clean($html);

// Redirect to another page
redirect('/dashboard');

// Get previous form input
$oldValue = old('email', 'default@example.com');
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.