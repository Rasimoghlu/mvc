<?php

use Core\Facades\Router;

Router::get('test', function () {
//    $req = \Core\Facades\Request::all();
//    dd($req);
//    $req = new \App\Http\Controllers\UserController();
//    dd($req->index());
});

Router::get('/', function () {
    echo 'salam';
});