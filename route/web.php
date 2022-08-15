<?php

use Core\Facades\Router;

Router::get('test', function () {

});

Router::get('/', function () {
    echo 'salam';
});