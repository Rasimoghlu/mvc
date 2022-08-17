<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use App\Interfaces\ResponseInterface;
use Core\Handlers\ResponseHandler;

class Response
{
    /**
     * @param ResponseInterface $responseHandler
     */
    public function __construct(private readonly ResponseInterface $responseHandler)
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static(new ResponseHandler()))->$name(...$arguments);
    }

    /**
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->responseHandler, $name)) {
            return $this->responseHandler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}