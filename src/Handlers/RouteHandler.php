<?php

namespace Src\Handlers;

use App\Interfaces\RouteInterface;

class RouteHandler implements RouteInterface
{
    /**
     * @var array
     */
    protected array $params;

    /**
     * @param string $route
     * @param callable|string $callback
     * @param $method
     * @return mixed|void
     */
    public function run(string $route, callable|string $callback, $method = 'get')
    {
        if ($this->checkPatternIsMatching($this->patterns($route), '/'.request('url'))) {

            if (is_callable($callback)) {
                call_user_func_array($callback, $this->params);
            }

            return $this->controllerPath($callback, $this->params);
        }
    }

    /**
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
     * @param $callback
     * @param $params
     * @return mixed|void
     */
    private function controllerPath($callback, $params)
    {
        $controller = explode('@', $callback);
        $controllerFile = __DIR__ . '/../../app/Http/Controllers/' . strtolower($controller[0]) . '.php';

        if (file_exists($controllerFile)) {
            require $controllerFile;

           return call_user_func_array([$this->addNameSpaceToClass($controller), $controller[1]], $params);
        }
    }

    /**
     * @param $class
     * @return mixed
     */
    private function addNameSpaceToClass($class)
    {
        $class = 'App\\Http\\Controllers\\' . $class[0];

        return new $class;
    }

    /**
     * @param $url
     * @return array|string
     */
    private function patterns($url): array|string
    {
        $patterns = [
            '{url}' => '([0-9a-zA-Z]+)',
            '{id}' => '([0-9]+)'
        ];

        return str_replace(array_keys($patterns), array_values($patterns), $url);
    }

}