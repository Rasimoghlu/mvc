<?php

namespace Src;

use App\Http\Exceptions\MethodNotFoundException;

class Facade
{
    private $handler;

    /**
     *
     */
    public function __construct()
    {
        $this->handler = $this->getFacadeAccessor();
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
        if (method_exists($this->handler, $name)) {
            return $this->handler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

    protected static function getFacadeAccessor()
    {
       return self::class;
    }

}