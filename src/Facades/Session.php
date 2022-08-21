<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\SessionHandler;

class Session extends Facade
{
    protected static function getFacadeAccessor(): SessionHandler
    {
        return new SessionHandler();
    }
}