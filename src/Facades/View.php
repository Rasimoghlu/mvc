<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\ViewHandler;

class View extends Facade
{
    protected static function getFacadeAccessor(): ViewHandler
    {
        return new ViewHandler();
    }
}