<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use Core\Handlers\RouteHandler;

class Router
{
    /**
     * @var RouteHandler
     */
    private RouteHandler $routeHandler;

    /**
     *
     */
    public function __construct()
    {
        $this->routeHandler = new RouteHandler();
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static())->$name(...$arguments);
    }

    /**
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->routeHandler, $name)) {
            return $this->routeHandler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}