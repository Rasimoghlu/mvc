<?php

namespace Bootstrap;

/**
 * Application Provider
 */
class Provider
{
    /**
     * Boots the application and returns the application instance
     *
     * @return mixed
     */
    public static function run()
    {
        return self::getProviders();
    }

    /**
     * Load application providers
     *
     * @return mixed
     */
    private static function getProviders()
    {
        $app = include_once '../config/app.php';
        
        foreach ($app['providers'] as $provider) {
            $provider::boot();
        }
        
        return $app;
    }
}