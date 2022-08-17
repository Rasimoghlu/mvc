<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use App\Interfaces\RouteInterface;
use Core\Handlers\RouteHandler;

class Router
{
    /**
     *
     */
    public function __construct(private readonly RouteInterface $routeHandler)
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static(new RouteHandler()))->$name(...$arguments);
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