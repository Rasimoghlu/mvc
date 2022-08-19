<?php

namespace App\Http\Controllers;

use App\Models\User;
use Core\Facades\Request;
use Core\Facades\Validation;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();

        return view('users', compact('users'));
    }

    public function store()
    {
        $request = Request::all();

        $rules = Validation::make($request, [
            'name' => 'string|required',
            'email' => 'email',
            'password' => 'string|required'
        ]);

        User::create($rules);
    }
}