<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\RequestHandler;

class Request extends Facade
{
    protected static function getFacadeAccessor(): RequestHandler
    {
        return new RequestHandler();
    }
}