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
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');

        if ($requestMethod !== strtolower($method)) {
            return;
        }

        if ($this->checkPatternIsMatching($this->patterns($route), $requestUrl)) {
            if (is_callable($callback)) {
                return call_user_func_array($callback, $this->params);
            }

            return $this->controllerPath($callback, $this->params);
        }
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

    /**
     * Controller runner
     * @throws \Exception
     */
    private function controllerPath($callback, $params)
    {
        $controller = explode('@', $callback);
        $controllerFile = __DIR__ . '/../../app/Http/Controllers/' . strtolower($controller[0]) . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            return call_user_func_array([$this->addNameSpaceToClass($controller), $controller[1]], $params);
        }

        throw new \Exception("Controller file not found: $controllerFile");
    }

    /**
     * Controller namespace
     */
    private function addNameSpaceToClass(array $class)
    {
        $className = 'App\\Http\\Controllers\\' . $class[0];
        return new $className;
    }

    /**
     * Route pattern convert regex
     */
    private function patterns($url): array|string
    {
        $patterns = [
            '{url}' => '([0-9a-zA-Z-_]+)',
            '{id}' => '([0-9]+)'
        ];

        return str_replace(array_keys($patterns), array_values($patterns), $url);
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