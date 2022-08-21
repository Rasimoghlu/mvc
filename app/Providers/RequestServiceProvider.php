<?php

namespace App\Providers;

use Bootstrap\Provider;
use Src\Facades\Request;

class RequestServiceProvider extends Provider
{
    public static function boot()
    {
        Request::handle();
    }
}