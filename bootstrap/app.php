<?php

namespace Bootstrap;

class Provider
{
    public static function run()
    {
        self::getProviders();
    }

    private static function getProviders()
    {
        $app = include_once '../config/app.php';

        foreach ($app['providers'] as $provider) {
            $provider::boot();
        }
    }

}