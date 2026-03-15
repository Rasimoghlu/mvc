<?php

namespace Src;

use Exception;
use App\Http\Exceptions\MethodNotFoundException;

/**
 * Base Facade class
 *
 * Provides a static interface to non-static methods of a class.
 * Uses singleton-style caching to avoid creating new instances on every call.
 */
abstract class Facade
{
    private static array $resolvedInstances = [];

    private $handler;

    public function __construct()
    {
        $this->handler = static::resolveFacadeInstance();
    }

    abstract protected static function getFacadeAccessor();

    protected static function resolveFacadeInstance(): object
    {
        $accessor = static::class;

        if (!isset(self::$resolvedInstances[$accessor])) {
            self::$resolvedInstances[$accessor] = static::getFacadeAccessor();
        }

        return self::$resolvedInstances[$accessor];
    }

    public static function __callStatic(string $method, array $args)
    {
        $instance = static::resolveFacadeInstance();

        if (!method_exists($instance, $method)) {
            throw new MethodNotFoundException("Method {$method} does not exist on " . get_class($instance));
        }

        return $instance->$method(...$args);
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->handler, $name)) {
            return $this->handler->$name(...$arguments);
        }

        throw new MethodNotFoundException("Method {$name} does not exist on " . get_class($this->handler));
    }
}
