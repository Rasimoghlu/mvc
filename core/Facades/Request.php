<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use App\Interfaces\RequestInterface;
use Core\Handlers\RequestHandler;

class Request
{
    /**
     *
     */
    public function __construct(private readonly RequestInterface $requestHandler)
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static(new RequestHandler()))->$name(...$arguments);
    }

    /**
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->requestHandler, $name)) {
            return $this->requestHandler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}