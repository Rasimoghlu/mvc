<?php

namespace Core\Handlers;

class RouteHandler
{
    private function resolveUri(): array|string
    {
        $requestUri = $_SERVER['REQUEST_URI'];

        $explodeRequestUri = explode('/', $requestUri);

        $routeName = end($explodeRequestUri);

        if(!$routeName){
            $routeName = '/';
        }

        return $routeName;
    }

    public function get(string $route, callable $callback): void
    {
        $requestUri = $this->resolveUri();

        if (str_contains($requestUri, $route))
        {
            if (is_callable($callback)) {
                call_user_func($callback);
            }
        }
    }

}