<?php

use Core\Facades\Router;


Router::run('/test', 'UserController@index', 'get');

Router::run('/test/store', 'UserController@store', 'post');
