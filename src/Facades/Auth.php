<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\AuthHandler;

/**
 * Auth Facade
 * 
 * Provides access to authentication functionality
 */
class Auth extends Facade
{
    /**
     * Get the facade accessor
     * 
     * @return AuthHandler
     */
    protected static function getFacadeAccessor(): AuthHandler
    {
        return new AuthHandler();
    }
}