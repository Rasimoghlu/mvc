<?php

namespace App\Providers;

use Bootstrap\Provider;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

/**
 * DotEnv Service Provider
 * 
 * Loads environment variables from .env file
 */
class DotEnvServiceProvider extends Provider
{
    /**
     * Bootstrap DotEnv service
     *
     * @return void
     */
    public static function boot(): void
    {
        try {
            $rootPath = __DIR__ . '/../../';
            $dotenv = Dotenv::createUnsafeImmutable($rootPath);
            $dotenv->load();
        } catch (InvalidPathException $e) {
            die('The .env file is missing or invalid. Please create one based on .env.example');
        }
    }
}