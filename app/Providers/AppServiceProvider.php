<?php

namespace App\Providers;

use App\Http\Exceptions\Whoops;
use Bootstrap\Provider;

class AppServiceProvider extends Provider
{
    public function register(){

    }

    public static function boot()
    {
        Whoops::handle();
    }
}