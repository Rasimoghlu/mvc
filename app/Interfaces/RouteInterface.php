<?php

namespace App\Interfaces;

interface RouteInterface
{
    public function run(string $route, callable|string $callback, $method = 'get');
}