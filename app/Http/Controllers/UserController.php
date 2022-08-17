<?php

namespace App\Http\Controllers;

use App\Models\User;
use Core\Facades\Request;
use Core\Facades\Response;
use Core\Facades\Session;

class UserController extends Controller
{
    public function index()
    {
//        dd(Request::method());
//        $users = User::findById($id);

        return view('users');
    }

    public function store()
    {
        $request = Request::all();

        User::create($request);
    }
}