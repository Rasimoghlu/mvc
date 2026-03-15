<?php

namespace App\Interfaces;

interface RouteInterface
{
    public function run(string $route, callable|string $callback, string $method = 'get'): mixed;

    public function middleware(string|array $middleware): static;
}
