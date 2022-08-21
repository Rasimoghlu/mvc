<?php

namespace App\Providers;

use Bootstrap\Provider;
use Src\Facades\Session;

class SessionServiceProvider extends Provider
{
    public static function boot()
    {
        Session::start();
    }
}