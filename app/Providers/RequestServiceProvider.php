<?php

namespace App\Providers;

use Bootstrap\Provider;
use Core\Facades\Request;

class RequestServiceProvider extends Provider
{
    /**
     * Application constructor
     */
    public function register(){

    }

    public static function boot()
    {
        Request::handle();
    }
}