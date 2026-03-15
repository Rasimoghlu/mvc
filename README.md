# MVC Framework

A lightweight MVC framework for PHP 8.3+ inspired by Laravel.

## Requirements

| Dependency | Version |
|------------|---------|
| PHP        | ^8.3    |
| ext-pdo    | *       |

## Installation

```bash
composer create-project rasimoghlu/mvc your-project-name
cd your-project-name
```

## Configuration

1. Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

2. Configure your environment variables:

```env
APP_ENV=local
APP_DEBUG=true
APP_NAME=MVC
APP_URL=http://mvc.test

DB_HOST=localhost
DB_NAME=mvc
DB_USER=root
DB_PASSWORD=
```

3. Run the application:

```bash
php -S localhost:8000
```

## Routing

Define routes in `route/web.php`:

```php
use Src\Facades\Router;

Router::run('/users', 'UserController@index', 'get');
Router::run('/users/create', 'UserController@create', 'get');
Router::run('/users/store', 'UserController@store', 'post');
Router::run('/users/{id}', 'UserController@update', 'put');
Router::run('/users/{id}', 'UserController@destroy', 'delete');

// With middleware
Router::middleware('auth')->run('/users/{id}', 'UserController@show', 'get');
Router::middleware(['auth', 'admin'])->run('/admin/dashboard', 'AdminController@index', 'get');

// With closure
Router::run('/ping', function () {
    echo 'pong';
}, 'get');
```

### Route Parameters

| Pattern          | Matches              |
|------------------|----------------------|
| `{id}`           | Digits (`[0-9]+`)    |
| `{slug}`         | Alphanumeric, dash, underscore |
| `{alpha}`        | Letters only         |
| `{alphanumeric}` | Letters and digits   |
| `{url}`          | Alphanumeric, dash, underscore |
| `{any}`          | Anything             |

### HTTP Method Override

For PUT, PATCH, DELETE requests use a hidden `_method` field in forms:

```html
<form method="POST" action="/users/1">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">
    <!-- form fields -->
</form>
```

## Middleware

Create middleware by implementing `MiddlewareInterface`:

```php
<?php

namespace App\Http\Middleware;

use App\Interfaces\MiddlewareInterface;
use Src\Facades\Auth;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        if (Auth::guest()) {
            redirect('/login');
        }

        return true;
    }
}
```

Register middleware aliases in `RouteHandler::$middlewareMap`:

```php
protected array $middlewareMap = [
    'auth'  => \App\Http\Middleware\AuthMiddleware::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
```

## Controllers

Controllers extend the base `Controller` class:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    protected User $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    public function index()
    {
        $users = $this->user->all();
        return $this->view('users/index', compact('users'));
    }

    public function show(int $id)
    {
        $user = User::findOrFail($id);
        return view('users/show', ['user' => $user]);
    }

    public function store()
    {
        $validated = $this->validate([
            'name'     => 'required|string|min:3|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (!$validated) {
            return $this->back();
        }

        $validated['password'] = password_hash($validated['password'], PASSWORD_DEFAULT);
        User::create($validated);

        return $this->withSuccess('User created successfully')
            ->redirect('/users');
    }
}
```

### Base Controller Methods

| Method                           | Description                    |
|----------------------------------|--------------------------------|
| `$this->view($name, $data)`     | Render a view                  |
| `$this->redirect($url)`         | Redirect to URL                |
| `$this->back()`                 | Redirect to previous page      |
| `$this->validate($rules)`       | Validate POST data             |
| `$this->withSuccess($msg)`      | Flash success message          |
| `$this->withError($msg)`        | Flash error message            |
| `$this->json($data, $status)`   | Send JSON response             |
| `$this->isAuthenticated()`      | Check if user is logged in     |
| `$this->user()`                 | Get authenticated user         |
| `$this->isAjax()`               | Check if AJAX request          |

## Models

Models extend `Src\Facades\Model`:

```php
<?php

namespace App\Models;

use Src\Facades\Model;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    protected bool $timestamps = true;
    protected string $createdField = 'created_at';
    protected string $updatedField = 'updated_at';
    protected array $fillable = ['name', 'email', 'password'];
    protected array $hidden = ['password'];
}
```

Timestamps are handled automatically if the columns exist in your table.

### CRUD Operations

```php
// Create
$user = User::create(['name' => 'John', 'email' => 'john@example.com']);

// Read
$users = User::all();
$user  = User::find(1);
$user  = User::findOrFail(1);      // throws ModelNotFoundException
$user  = User::where('email', '=', 'john@example.com')->first();
$count = User::count();

// Update
User::update(1, ['name' => 'Jane']);

// Delete
User::delete(1);
```

### Query Builder

```php
// Where clauses
User::where('active', '=', 1)->get();
User::where('active', '=', 1)->orWhere('role', '=', 'admin')->get();
User::whereIn('role', ['admin', 'editor'])->get();
User::whereNotIn('status', ['banned', 'suspended'])->get();
User::whereNull('deleted_at')->get();
User::whereNotNull('email_verified_at')->get();
User::whereBetween('age', 18, 65)->get();

// Date queries
User::whereDate('created_at', '>', '2024-01-01')->get();
User::whereMonth('created_at', '=', 12)->get();
User::whereYear('created_at', '=', 2024)->get();

// Joins
User::select(['users.name', 'posts.title'])
    ->join('posts', 'posts.user_id', '=', 'users.id')
    ->get();

User::select(['users.name', 'COUNT(posts.id) as post_count'])
    ->leftJoin('posts', 'posts.user_id', '=', 'users.id')
    ->groupBy('users.name')
    ->having('post_count', '>', 5)
    ->get();

// Ordering and limiting
User::where('active', '=', 1)
    ->orderBy('name', 'ASC')
    ->orderByDesc('created_at')
    ->limit(10)
    ->offset(20)
    ->get();

// Pagination
$result = User::where('active', '=', 1)->paginate(15);
// $result->page, $result->totalPage, $result->hasMorePages, etc.

// Raw expressions
User::whereRaw('YEAR(created_at) = ?', [2024])->get();
```

## Validation

### In Controllers

```php
$validated = $this->validate([
    'name'     => 'required|string|min:3|max:100',
    'email'    => 'required|email',
    'password' => 'required|min:8|confirmed',
    'bio'      => 'sometimes|string|max:500',
]);

if (!$validated) {
    return $this->back();
}
```

### Form Request Classes

```php
<?php

namespace App\Http\Requests;

use Src\Handlers\ValidationHandler;

class UserStoreRequest
{
    protected ValidationHandler $validator;
    protected ?array $validatedData = null;

    public function __construct()
    {
        $this->validator = new ValidationHandler();
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:3|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ];
    }

    public function validate(): bool
    {
        $this->validatedData = $this->validator->make($_POST, $this->rules());
        return $this->validatedData !== false;
    }

    public function validated(): ?array
    {
        return $this->validatedData;
    }

    public function failedValidation()
    {
        return $this->validator->returnBackWithValidationErrors();
    }
}
```

### Available Rules

| Rule           | Description                                      | Example              |
|----------------|--------------------------------------------------|----------------------|
| `required`     | Field must be present and not empty               | `required`           |
| `sometimes`    | Only validate if field is present in data         | `sometimes\|email`   |
| `string`       | Must be a string                                  | `string`             |
| `integer`      | Must be an integer                                | `integer`            |
| `numeric`      | Must be numeric                                   | `numeric`            |
| `email`        | Must be a valid email                             | `email`              |
| `min`          | Minimum string length                             | `min:8`              |
| `max`          | Maximum string length                             | `max:255`            |
| `between`      | String length between min and max                 | `between:3,100`      |
| `in`           | Value must be in given list                       | `in:admin,user`      |
| `regex`        | Must match regex pattern                          | `regex:/^[A-Z]/`     |
| `confirmed`    | Must have matching `_confirmation` field          | `confirmed`          |
| `alpha`        | Letters only                                      | `alpha`              |
| `alpha_num`    | Letters and numbers only                          | `alpha_num`          |
| `alpha_dash`   | Letters, numbers, dashes, underscores             | `alpha_dash`         |
| `date`         | Valid date (YYYY-MM-DD)                           | `date`               |
| `url`          | Valid URL                                         | `url`                |
| `ip`           | Valid IP address                                  | `ip`                 |
| `json`         | Valid JSON string                                 | `json`               |
| `phone`        | Valid phone number format                         | `phone`              |

### Displaying Errors in Views

```php
<?php if ($nameError = error('name')): ?>
    <div class="error"><?= e($nameError) ?></div>
<?php endif; ?>

<input type="text" name="name" value="<?= e(old('name')) ?>">
```

Call `clearErrors()` and `clearOld()` at the end of your view to clean up flash data.

## Authentication

```php
use Src\Facades\Auth;

// Login
$result = Auth::login(['email' => $email, 'password' => $password]);

// Check authentication
if (Auth::check()) {
    $user = Auth::user();
}

// Check if guest
if (Auth::guest()) {
    redirect('/login');
}

// Check role
if (Auth::hasRole('admin')) {
    // admin only
}

// Logout
Auth::logout();
```

## Session

```php
use Src\Facades\Session;

Session::set('key', 'value');
$value = Session::get('key', 'default');
Session::has('key');        // bool
Session::remove('key');     // removes single key
Session::clear();           // removes all session data
Session::regenerate();      // regenerate session ID
```

## CSRF Protection

All POST forms must include a CSRF token:

```html
<form method="POST" action="/users/store">
    <?= csrf_field() ?>
    <!-- form fields -->
</form>
```

POST requests without a valid CSRF token will receive a `403 Forbidden` response.

## Views

Views are stored in the `view/` directory. Always escape output with `e()`:

```php
<h1><?= e($user->name) ?></h1>
<p><?= e($user->email) ?></p>
```

## Helper Functions

| Function          | Description                           |
|-------------------|---------------------------------------|
| `view($name, $data)` | Render a view                     |
| `redirect($url)`     | Redirect to URL                   |
| `request($key)`      | Get request value                 |
| `e($value)`          | Escape HTML (XSS protection)     |
| `old($key, $default)`| Get previous form input           |
| `error($key)`        | Get validation error for field    |
| `clearErrors()`      | Clear flash validation errors     |
| `clearOld()`         | Clear flash old input             |
| `csrf_field()`       | Generate hidden CSRF input        |
| `_token()`           | Get CSRF token value              |
| `dd(...$data)`       | Dump and die                      |
| `dump($data)`        | Dump variable                     |
| `class_basename($class)` | Get class name without namespace |
| `snake_case($input)` | Convert to snake_case             |

## Service Providers

Service providers are registered in `config/app.php`:

```php
return [
    'providers' => [
        App\Providers\DotEnvServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\SessionServiceProvider::class,
        App\Providers\RequestServiceProvider::class,
    ]
];
```

`DotEnvServiceProvider` must be first to ensure environment variables are available to other providers.

## Directory Structure

```
mvc/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Exceptions/
│   │   ├── Functions/helpers.php
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Interfaces/
│   ├── Models/
│   ├── Providers/
│   └── Traits/
├── bootstrap/app.php
├── config/app.php
├── public/index.php
├── route/web.php
├── src/
│   ├── DatabaseConnection.php
│   ├── Facade.php
│   ├── Facades/
│   └── Handlers/
├── view/
│   ├── errors/
│   └── users/
├── index.php
├── .env
├── .env.example
└── composer.json
```

## Security

- CSRF protection on all POST requests
- XSS prevention via `e()` helper
- Prepared statements for all database queries
- Session hardening (httponly, secure, samesite, strict mode)
- Session regeneration on login
- Open redirect protection
- Debug mode gated behind `APP_DEBUG`
- Passwords hashed with `password_hash(PASSWORD_DEFAULT)`

## License

MIT
