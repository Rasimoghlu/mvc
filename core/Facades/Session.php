<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use Core\Handlers\SessionHandler;

class Session
{
    /**
     * @var SessionHandler
     */
    private SessionHandler $sessionHandler;

    /**
     *
     */
    public function __construct()
    {
        $this->sessionHandler = new SessionHandler();
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
        if (method_exists($this->sessionHandler, $name)) {
            return $this->sessionHandler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}