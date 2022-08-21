<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\ResponseHandler;

class Response extends Facade
{
    protected static function getFacadeAccessor(): ResponseHandler
    {
        return new ResponseHandler();
    }
}