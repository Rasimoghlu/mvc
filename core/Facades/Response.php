<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use Core\Handlers\ResponseHandler;

class Response
{
    /**
     * @var ResponseHandler
     */
    private ResponseHandler $responseHandler;

    /**
     *
     */
    public function __construct()
    {
        $this->responseHandler = new ResponseHandler();
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
        if (method_exists($this->responseHandler, $name)) {
            return $this->responseHandler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}