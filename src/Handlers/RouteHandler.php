<?php

namespace Src\Handlers;

use App\Interfaces\RouteInterface;
use Exception;

class RouteHandler implements RouteInterface
{
    /**
     * @var array
     */
    protected array $params = [];
    
    /**
     * @var array
     */
    protected array $routes = [];
    
    /**
     * @var array
     */
    protected array $patterns = [
        '{url}' => '([0-9a-zA-Z-_]+)',
        '{id}' => '([0-9]+)',
        '{slug}' => '([0-9a-zA-Z-_]+)',
        '{alpha}' => '([a-zA-Z]+)',
        '{alphanumeric}' => '([a-zA-Z0-9]+)',
        '{any}' => '(.*)'
    ];

    /**
     * Get the request method, taking into account method overrides via _method parameter
     *
     * @return string
     */
    private function getRequestMethod(): string
    {
        $method = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');
        
        // Check for method override via _method parameter in POST requests
        if ($method === 'post' && isset($_POST['_method'])) {
            $overrideMethod = strtolower($_POST['_method']);
            
            // Only allow valid HTTP methods
            if (in_array($overrideMethod, ['put', 'delete', 'patch'])) {
                return $overrideMethod;
            }
        }
        
        return $method;
    }

    /**
     * Route runner
     *
     * @param string $route
     * @param callable|string $callback
     * @param string $method
     * @return mixed|void
     * @throws Exception
     */
    public function run(string $route, callable|string $callback, string $method = 'get')
    {
        $requestUrl = $this->getRequestPath();
        $requestMethod = $this->getRequestMethod();

        // Store route for potential use with middleware
        $this->routes[$method][$route] = [
            'callback' => $callback,
            'middleware' => []
        ];

        if ($requestMethod !== strtolower($method)) {
            return;
        }

        if ($this->checkPatternIsMatching($this->applyPatterns($route), $requestUrl)) {
            if (is_callable($callback)) {
                return call_user_func_array($callback, $this->params);
            }

            return $this->controllerPath($callback, $this->params);
        }
    }

    /**
     * Add middleware to a route
     *
     * @param string $route
     * @param string $method
     * @param string|array $middleware
     * @return void
     */
    public function middleware(string $route, string $method, string|array $middleware): void
    {
        $method = strtolower($method);
        
        if (isset($this->routes[$method][$route])) {
            $this->routes[$method][$route]['middleware'] = is_array($middleware) 
                ? $middleware 
                : [$middleware];
        }
    }

    /**
     * Check if pattern matches request URL
     *
     * @param string $route
     * @param string $requestUrl
     * @return bool
     */
    private function checkPatternIsMatching(string $route, string $requestUrl): bool
    {
        if (preg_match('@^' . $route . '$@', $requestUrl, $params)) {
            unset($params[0]);
            $this->params = $params;
            return true;
        }

        return false;
    }

    /**
     * Controller runner
     * 
     * @param string $callback
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    private function controllerPath($callback, $params)
    {
        $controller = explode('@', $callback);
        $controllerName = $controller[0];
        $controllerMethod = $controller[1] ?? 'index';
        
        $controllerClass = 'App\\Http\\Controllers\\' . $controllerName;
        
        if (!class_exists($controllerClass)) {
            $controllerFile = __DIR__ . '/../../app/Http/Controllers/' . $controllerName . '.php';
            
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller file not found: $controllerFile");
            }
            
            require_once $controllerFile;
            
            if (!class_exists($controllerClass)) {
                throw new Exception("Controller class not found: $controllerClass");
            }
        }
        
        $controllerInstance = new $controllerClass();
        
        if (!method_exists($controllerInstance, $controllerMethod)) {
            throw new Exception("Method {$controllerMethod} not found in controller {$controllerClass}");
        }
        
        return call_user_func_array([$controllerInstance, $controllerMethod], $params);
    }

    /**
     * Apply patterns to route
     *
     * @param string $url
     * @return string
     */
    private function applyPatterns(string $url): string
    {
        return str_replace(array_keys($this->patterns), array_values($this->patterns), $url);
    }

    /**
     * Get the request path
     *
     * @return string
     */
    private function getRequestPath(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);

        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        return '/' . trim($uri, '/');
    }
}