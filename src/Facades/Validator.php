<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\ValidationHandler;

/**
 * Validator Facade
 * 
 * Provides access to the ValidationHandler
 */
class Validator extends Facade
{
    /**
     * Get the facade accessor
     * 
     * @return ValidationHandler
     */
    protected static function getFacadeAccessor(): ValidationHandler
    {
        return new ValidationHandler();
    }
} 