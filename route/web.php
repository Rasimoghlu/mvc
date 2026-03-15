<?php

use Src\Facades\Router;

/**
 * Web Routes
 */

// Home — redirect to login
Router::run('/', function () {
    redirect('/login');
}, 'get');

// Auth (guest only)
Router::middleware('guest')->run('/login', 'AuthController@loginForm', 'get');
Router::middleware('guest')->run('/login', 'AuthController@login', 'post');
Router::middleware('guest')->run('/register', 'AuthController@registerForm', 'get');
Router::middleware('guest')->run('/register', 'AuthController@register', 'post');
Router::middleware('auth')->run('/logout', 'AuthController@logout', 'post');

// Tasks (auth required)
Router::middleware('auth')->run('/tasks', 'TaskController@index', 'get');
Router::middleware('auth')->run('/tasks/create', 'TaskController@create', 'get');
Router::middleware('auth')->run('/tasks/store', 'TaskController@store', 'post');
Router::middleware('auth')->run('/tasks/{id}', 'TaskController@show', 'get');
Router::middleware('auth')->run('/tasks/{id}/edit', 'TaskController@edit', 'get');
Router::middleware('auth')->run('/tasks/{id}', 'TaskController@update', 'put');
Router::middleware('auth')->run('/tasks/{id}', 'TaskController@destroy', 'delete');

// Users (existing)
Router::run('/users', 'UserController@index', 'get');
Router::run('/users/create', 'UserController@create', 'get');
Router::run('/users/store', 'UserController@store', 'post');
Router::run('/users/{id}/edit', 'UserController@edit', 'get');
Router::run('/users/{id}', 'UserController@update', 'put');
Router::run('/users/{id}', 'UserController@destroy', 'delete');
Router::middleware('auth')->run('/users/{id}', 'UserController@show', 'get');
