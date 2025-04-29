<?php

namespace Src\Facades;

use Src\Facade;
use Src\Handlers\RequestHandler;

/**
 * Request Facade
 * 
 * Provides access to HTTP request information and data
 */
class Request extends Facade
{
    /**
     * Get the facade accessor
     * 
     * @return RequestHandler
     */
    protected static function getFacadeAccessor(): RequestHandler
    {
        return new RequestHandler();
    }
}