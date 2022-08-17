<?php

namespace Core\Handlers;

class RouteHandler
{
    protected array $params;

    public function run(string $route, callable|string $callback, $method = 'get')
    {
        if ($this->checkPatternIsMatching($this->patterns($route), '/'.request('url'))) {

            if (is_callable($callback)) {
                call_user_func_array($callback, $this->params);
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

    private function controllerPath($callback, $params)
    {
        $controller = explode('@', $callback);
        $controllerFile = __DIR__ . '/../../app/Http/Controllers/' . strtolower($controller[0]) . '.php';

        if (file_exists($controllerFile)) {
            require $controllerFile;

           return call_user_func_array([$this->addNameSpaceToClass($controller), $controller[1]], $params);
        }
    }

    private function addNameSpaceToClass($class)
    {
        $class = 'App\\Http\\Controllers\\' . $class[0];

        return new $class;
    }

    private function patterns($url): array|string
    {
        $patterns = [
            '{url}' => '([0-9a-zA-Z]+)',
            '{id}' => '([0-9]+)'
        ];

        return str_replace(array_keys($patterns), array_values($patterns), $url);
    }

}