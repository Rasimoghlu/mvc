<?php

namespace App\Http\Controllers;

use App\Models\User;
use Core\Facades\Auth;
use Core\Facades\Request;
use Core\Facades\Response;
use Core\Facades\Session;
use Core\Facades\Validation;

class UserController extends Controller
{
    public function index()
    {
//        Auth::login([
//            'email' => 'test@gmail.com2',
//            'password' => 'password'
//        ]);

//        $users = User::findById($id);

        return view('users');
    }

    public function store()
    {
        $request = Request::all();

        $rules = Validation::make($request, [
            'name' => 'required',
            'email' => 'email',
            'password' => 'required'
        ]);
    dd($rules);

//        User::create($request);
    }
}