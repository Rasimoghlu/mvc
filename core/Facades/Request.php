<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use Core\Handlers\RequestHandler;

class Request
{
    /**
     * @var RequestHandler
     */
    private RequestHandler $requestHandler;

    /**
     *
     */
    public function __construct()
    {
        $this->requestHandler = new RequestHandler();
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
        if (method_exists($this->requestHandler, $name)) {
            return $this->requestHandler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}