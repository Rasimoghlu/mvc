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
- Automatic handling of timestamps with graceful fallback
- Safe database operations with optional fields

## Creating Models

Models provide an elegant way to interact with your database. Each model represents a table in your database.

```php
<?php

namespace App\Models;

use Src\Facades\Model;

class User extends Model
{
    protected string $table = 'users';
    
    // Optional: Override timestamp settings
    protected bool $timestamps = true;
    protected string $createdField = 'created_at';
    protected string $updatedField = 'updated_at';
    
    // Define fillable fields (fields that can be mass-assigned)
    protected array $fillable = ['name', 'email', 'password'];
}
```

### Timestamps
The framework automatically handles timestamps (`created_at` and `updated_at`) if they exist in your database tables. If the columns don't exist, the framework will continue without error.

## Creating Controllers

Controllers handle incoming HTTP requests and return responses.

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Exception;

class UserController extends Controller
{
    protected User $user;
    
    public function __construct()
    {
        $this->user = new User();
    }
    
    public function index()
    {
        $users = $this->user->all();
        return $this->view('users/index', compact('users'));
    }
    
    public function show(int $id)
    {
        try {
            $user = User::findOrFail($id);
            return view('users/show', [
                'user' => $user
            ]);
        } catch (Exception $e) {
            return view('errors/404', [
                'message' => 'User not found'
            ]);
        }
    }
    
    public function store()
    {
        try {
            $request = new UserStoreRequest();
            
            if (!$request->validate()) {
                return $request->failedValidation();
            }
            
            $data = $request->validated();
            
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $user = $this->user->create($data);
            
            if ($user) {
                return $this->withSuccess('User created successfully')
                    ->redirect('/users');
            }
            
            return $this->withError('Failed to create user')
                ->redirect('/users/create');
        } catch (Exception $e) {
            return $this->withError('An error occurred: ' . $e->getMessage())
                ->redirect('/users/create');
        }
    }
    
    public function update($id)
    {
        try {
            $user = $this->user->find($id);
            
            if (!$user) {
                return $this->withError('User not found')
                    ->redirect('/users');
            }
            
            $request = new UserUpdateRequest();
            
            if (!$request->validate()) {
                return $request->failedValidation();
            }
            
            $data = $request->validated();
            
            // Using static method for update
            $success = User::update($id, $data);
            
            if ($success) {
                return $this->withSuccess('User updated successfully')
                    ->redirect('/users');
            }
            
            return $this->withError('Failed to update user')
                ->redirect('/users/edit/' . $id);
        } catch (Exception $e) {
            return $this->withError('An error occurred: ' . $e->getMessage())
                ->redirect('/users/edit/' . $id);
        }
    }
    
    public function destroy($id)
    {
        try {
            $user = $this->user->find($id);
            
            if (!$user) {
                return $this->withError('User not found')
                    ->redirect('/users');
            }
            
            // Using static method for delete
            $success = User::delete($id);
            
            if ($success) {
                return $this->withSuccess('User deleted successfully')
                    ->redirect('/users');
            }
            
            return $this->withError('Failed to delete user')
                ->redirect('/users');
        } catch (Exception $e) {
            return $this->withError('An error occurred: ' . $e->getMessage())
                ->redirect('/users');
        }
    }
}
```

## Form Request Validation

Create specific classes for form requests:

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
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8'
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

## Models: Static vs Instance Methods

For CRUD operations, you can use both static and instance methods:

```php
// Static methods
$user = User::find(1);
User::update(1, ['name' => 'New Name']);
User::delete(1);

// Instance methods (note: fetch an instance first)
$userModel = new User();
$users = $userModel->all();
```

**Note:** For update and delete operations, always use static methods to avoid the "Call to undefined method stdClass::update()" error, as find() returns a stdClass object, not a model instance.

## Routing

Define your routes in the `route/web.php` file:

```php
<?php

use Src\Facades\Router;

// Basic routes
Router::run('/users', 'UserController@index', 'get');
Router::run('/users/create', 'UserController@create', 'get');
Router::run('/users/store', 'UserController@store', 'post');

// Routes with parameters
Router::run('/users/{id}', 'UserController@show', 'get');
Router::run('/users/{id}/edit', 'UserController@edit', 'get');
Router::run('/users/{id}', 'UserController@update', 'put');
Router::run('/users/{id}', 'UserController@destroy', 'delete');

// Apply middleware to a route
Router::middleware('/users/{id}', 'get', 'auth');
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

## Views

Create views in the `view/` directory and use them in your controllers:

```php
// From a controller
return $this->view('users/index', compact('users'));

// Or use the helper function
return view('users/edit', ['user' => $user]);
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