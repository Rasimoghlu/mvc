<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Src\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function loginForm()
    {
        return $this->view('auth/login');
    }

    public function login()
    {
        $request = new LoginRequest();

        if (!$request->validate()) {
            return $request->failedValidation();
        }

        $data = $request->validated();
        $result = Auth::login($data);

        if (is_object($result)) {
            return $this->redirect('/tasks');
        }

        return $this->withError($result)->redirect('/login');
    }

    public function registerForm()
    {
        return $this->view('auth/register');
    }

    public function register()
    {
        $request = new RegisterRequest();

        if (!$request->validate()) {
            return $request->failedValidation();
        }

        $data = $request->validated();

        $existing = User::where('email', '=', $data['email'])->first();
        if ($existing) {
            return $this->withError('This email is already registered.')->redirect('/register');
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = (new User())->create($data);

        if ($user) {
            Auth::login([
                'email' => $request->validated()['email'],
                'password' => $request->validated()['password'],
            ]);
            // password is already hashed, login manually via session
            return $this->redirect('/tasks');
        }

        return $this->withError('Registration failed. Please try again.')->redirect('/register');
    }

    public function logout()
    {
        Auth::logout();
        return $this->redirect('/login');
    }
}
