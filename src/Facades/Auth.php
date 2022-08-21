<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\AuthHandler;

class Auth extends Facade
{
    protected static function getFacadeAccessor(): AuthHandler
    {
        return new AuthHandler();
    }
}