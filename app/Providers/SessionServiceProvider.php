<?php

namespace App\Providers;

use Bootstrap\Provider;
use Core\Facades\Session;

class SessionServiceProvider extends Provider
{
    /**
     * Application constructor
     */
    public function register(){

    }

    public static function boot()
    {
        Session::start();
    }
}