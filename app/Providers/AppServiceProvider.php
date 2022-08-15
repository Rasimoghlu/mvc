<?php

namespace App\Providers;

use App\Http\Exceptions\Whoops;
use Bootstrap\Provider;
use Core\Facades\Request;

class AppServiceProvider extends Provider
{
    /**
     * Application constructor
     */
    public function register(){

    }

    public static function boot()
    {
        Whoops::handle();
        Request::handle();
    }
}