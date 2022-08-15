<?php

namespace App\Http\Exceptions;

class Whoops
{
    private function __construct(){}

    public static function handle()
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }
}