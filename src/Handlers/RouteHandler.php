<?php

namespace Src\Handlers;

use App\Interfaces\MiddlewareInterface;
use App\Interfaces\RouteInterface;
use Exception;

class RouteHandler implements RouteInterface
{
    protected array $params = [];
    protected array $routes = [];
    protected array $pendingMiddleware = [];

    protected array $patterns = [
        '{url}' => '([0-9a-zA-Z-_]+)',
        '{id}' => '([0-9]+)',
        '{slug}' => '([0-9a-zA-Z-_]+)',
        '{alpha}' => '([a-zA-Z]+)',
        '{alphanumeric}' => '([a-zA-Z0-9]+)',
        '{any}' => '(.*)'
    ];

    protected array $middlewareMap = [
        'auth' => \App\Http\Middleware\AuthMiddleware::class,
        'guest' => \App\Http\Middleware\GuestMiddleware::class,
    ];

    private function getRequestMethod(): string
    {
        $method = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');

        if ($method === 'post' && isset($_POST['_method'])) {
            $overrideMethod = strtolower($_POST['_method']);

            if (in_array($overrideMethod, ['put', 'delete', 'patch'])) {
                return $overrideMethod;
            }
        }

        return $method;
    }

    public function middleware(string|array $middleware): static
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        $this->pendingMiddleware = array_merge($this->pendingMiddleware, $middlewares);

        return $this;
    }

    public function run(string $route, callable|string $callback, string $method = 'get'): mixed
    {
        $currentMiddleware = $this->pendingMiddleware;
        $this->pendingMiddleware = [];

        $requestUrl = $this->getRequestPath();
        $requestMethod = $this->getRequestMethod();

        $this->routes[$method][$route] = [
            'callback' => $callback,
            'middleware' => $currentMiddleware,
        ];

        if ($requestMethod !== strtolower($method)) {
            return null;
        }

        if (!$this->checkPatternIsMatching($this->applyPatterns($route), $requestUrl)) {
            return null;
        }

        if (!$this->runMiddleware($currentMiddleware)) {
            return null;
        }

        if (is_callable($callback)) {
            return call_user_func_array($callback, $this->params);
        }

        return $this->controllerPath($callback, $this->params);
    }

    private function runMiddleware(array $middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            $class = $this->middlewareMap[$middleware] ?? $middleware;

            if (!class_exists($class)) {
                throw new Exception("Middleware class not found: {$class}");
            }

            $instance = new $class();

            if (!$instance instanceof MiddlewareInterface) {
                throw new Exception("Middleware must implement MiddlewareInterface: {$class}");
            }

            if (!$instance->handle()) {
                return false;
            }
        }

        return true;
    }

    private function checkPatternIsMatching(string $route, string $requestUrl): bool
    {
        if (preg_match('@^' . $route . '$@', $requestUrl, $params)) {
            unset($params[0]);
            $this->params = $params;
            return true;
        }

        return false;
    }

    private function controllerPath(string $callback, array $params): mixed
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

    private function applyPatterns(string $url): string
    {
        return str_replace(array_keys($this->patterns), array_values($this->patterns), $url);
    }

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
