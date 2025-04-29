<?php

namespace Src;

use Exception;
use App\Http\Exceptions\MethodNotFoundException;

/**
 * Base Facade class
 * 
 * Provides a static interface to non-static methods of a class
 */
abstract class Facade
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
     * Get the registered name of the component
     *
     * @return mixed
     */
    abstract protected static function getFacadeAccessor();

    /**
     * Handle dynamic, static calls to the object
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic(string $method, array $args)
    {
        $instance = static::getFacadeAccessor();

        if (!$instance) {
            throw new Exception('A facade root has not been set.');
        }

        if (!method_exists($instance, $method)) {
            throw new Exception("Method {$method} does not exist on " . get_class($instance));
        }

        return $instance->$method(...$args);
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
}