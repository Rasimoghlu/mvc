<?php

use Src\Facades\Router;

/**
 * Web Routes
 * 
 * Define your routes for the web interface here
 */

Router::run('/users', 'UserController@index', 'get');
Router::run('/users/{id}', 'UserController@show', 'get');
Router::run('/users/create', 'UserController@create', 'get');
Router::run('/users/store', 'UserController@store', 'post');
Router::run('/users/{id}/edit', 'UserController@edit', 'get');
Router::run('/users/{id}', 'UserController@update', 'put');
Router::run('/users/{id}', 'UserController@destroy', 'delete');

// Apply middleware to a route
Router::middleware('/users/{id}', 'get', 'auth');