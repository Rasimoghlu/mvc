<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use Core\Handlers\AuthHandler;

class Auth
{
    private AuthHandler $authHandler;

    public function __construct()
    {
        $this->authHandler = new AuthHandler();
    }

    public static function __callStatic(string $name, array $data)
    {
        return (new static())->$name(...$data);
    }

    /**
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->authHandler, $name)) {
            return $this->authHandler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}