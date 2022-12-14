<?php

namespace App\Providers;

use Bootstrap\Provider;

class DotEnvServiceProvider extends Provider
{
    public static function boot()
    {
        $env = \Dotenv\Dotenv::createUnsafeImmutable( __DIR__ . '/../../');
        $env->load();
    }
}