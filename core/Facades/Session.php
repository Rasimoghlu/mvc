<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use App\Interfaces\SessionInterface;
use Core\Handlers\SessionHandler;

class Session
{
    public function __construct(private readonly SessionInterface $sessionHandler)
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static(new SessionHandler()))->$name(...$arguments);
    }

    /**
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->sessionHandler, $name)) {
            return $this->sessionHandler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}