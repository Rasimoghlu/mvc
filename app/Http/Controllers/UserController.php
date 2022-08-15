<?php

namespace App\Http\Controllers;

use App\Models\User;
use Core\Facades\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();

        return Response::json($users);
    }
}