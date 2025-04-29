<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\RouteHandler;

/**
 * Router Facade
 * 
 * Provides a static interface to the RouteHandler class
 */
class Router extends Facade
{
    /**
     * Get the registered name of the component
     *
     * @return RouteHandler
     */
    protected static function getFacadeAccessor(): RouteHandler
    {
        return new RouteHandler();
    }
}