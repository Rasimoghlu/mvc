<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use App\Interfaces\AuthInterface;
use Core\Handlers\AuthHandler;

class Auth
{
    public function __construct(private readonly AuthInterface $authHandler)
    {
    }

    public static function __callStatic(string $name, array $data)
    {
        return (new static(new AuthHandler()))->$name(...$data);
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