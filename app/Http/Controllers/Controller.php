<?php

namespace App\Http\Controllers;

use Src\Facades\Auth;
use Src\Facades\Request;
use Src\Facades\Session;
use Src\Handlers\ValidationHandler;

/**
 * Base Controller
 * 
 * All application controllers should extend this class.
 * Provides common functionality for controllers.
 */
class Controller
{
    /**
     * The validator instance
     *
     * @var ValidationHandler|null
     */
    protected ?ValidationHandler $validator = null;
    
    /**
     * Response data for the view
     *
     * @var array
     */
    protected array $data = [];
    
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        // Initialize any common properties or services here
        $this->validator = new ValidationHandler();
    }
    
    /**
     * Validate request data against the given rules
     *
     * @param array $rules Validation rules
     * @param array $data Data to validate (defaults to $_POST)
     * @return array|bool Validated data or false if validation fails
     */
    protected function validate(array $rules, array $data = []): array|bool
    {
        if (empty($data)) {
            $data = $_POST;
        }
        
        return $this->validator->make($data, $rules);
    }
    
    /**
     * Add data to be passed to the view
     *
     * @param string $key Data key
     * @param mixed $value Data value
     * @return $this
     */
    protected function with(string $key, $value): self
    {
        $this->data[$key] = $value;
        
        return $this;
    }
    
    /**
     * Add success message to the response
     *
     * @param string $message Success message
     * @return $this
     */
    protected function withSuccess(string $message): self
    {
        $this->data['success'] = $message;
        Session::set('success', $message);
        
        return $this;
    }
    
    /**
     * Add error message to the response
     *
     * @param string $message Error message
     * @return $this
     */
    protected function withError(string $message): self
    {
        $this->data['error'] = $message;
        Session::set('error', $message);
        
        return $this;
    }
    
    /**
     * Render a view with the associated data
     *
     * @param string $view View name
     * @param array $additionalData Additional data to merge with existing data
     * @return mixed View response
     */
    protected function view(string $view, array $additionalData = [])
    {
        $data = array_merge($this->data, $additionalData);
        
        return view($view, $data);
    }
    
    /**
     * Redirect to a URL
     *
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code
     * @return never
     */
    protected function redirect(string $url, int $statusCode = 302)
    {
        return redirect($url, $statusCode);
    }
    
    /**
     * Redirect back to the previous page
     *
     * @param int $statusCode HTTP status code
     * @return never
     */
    protected function back(int $statusCode = 302)
    {
        return redirect($_SERVER['HTTP_REFERER'] ?? '/', $statusCode);
    }
    
    /**
     * Check if the current request is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return Auth::check();
    }
    
    /**
     * Get the authenticated user
     *
     * @return object|null
     */
    protected function user(): ?object
    {
        return Auth::user();
    }
    
    /**
     * Check if the current request is a specific HTTP method
     *
     * @param string $method HTTP method to check (GET, POST, PUT, DELETE)
     * @return bool
     */
    protected function isMethod(string $method): bool
    {
        return strtoupper(Request::method()) === strtoupper($method);
    }
    
    /**
     * Check if the current request is an AJAX request
     *
     * @return bool
     */
    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get the HTTP referer URL
     *
     * @param string $default Default URL if referer is not available
     * @return string
     */
    protected function referer(string $default = '/'): string
    {
        return $_SERVER['HTTP_REFERER'] ?? $default;
    }
    
    /**
     * Send JSON response
     *
     * @param array $data Data to encode as JSON
     * @param int $statusCode HTTP status code
     * @return never
     */
    protected function json(array $data, int $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}