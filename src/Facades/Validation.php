<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\ValidationHandler;

class Validation extends Facade
{
    protected static function getFacadeAccessor(): ValidationHandler
    {
        return new ValidationHandler();
    }
}