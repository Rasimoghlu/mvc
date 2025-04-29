<?php

namespace App\Interfaces;

/**
 * Route Interface
 * 
 * Contract for route handlers
 */
interface RouteInterface
{
    /**
     * Register and run a route
     *
     * @param string $route
     * @param callable|string $callback
     * @param string $method
     * @return mixed
     */
    public function run(string $route, callable|string $callback, string $method = 'get');
    
    /**
     * Apply middleware to a route
     *
     * @param string $route
     * @param string $method
     * @param string|array $middleware
     * @return void
     */
    public function middleware(string $route, string $method, string|array $middleware): void;
}