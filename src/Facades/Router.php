<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\RouteHandler;

class Router extends Facade
{
    protected static function getFacadeAccessor(): RouteHandler
    {
        return new RouteHandler();
    }
}