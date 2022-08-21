<?php

namespace App\Providers;

use App\Http\Exceptions\Whoops;
use Bootstrap\Provider;
use Src\Facades\Model;

class AppServiceProvider extends Provider
{
    public static function boot()
    {
        Whoops::handle();
    }
}