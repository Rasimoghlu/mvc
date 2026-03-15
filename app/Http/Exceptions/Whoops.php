<?php

namespace App\Http\Exceptions;

class Whoops
{
    private function __construct(){}

    public static function handle(): void
    {
        $whoops = new \Whoops\Run;

        if (getenv('APP_ENV') === 'local' && getenv('APP_DEBUG') === 'true') {
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        } else {
            $whoops->pushHandler(function () {
                http_response_code(500);
                include __DIR__ . '/../../../view/errors/500.php';
            });
        }

        $whoops->register();
    }
}