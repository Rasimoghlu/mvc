<?php

namespace App\Http\Middleware;

use App\Interfaces\MiddlewareInterface;
use Src\Facades\Auth;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        if (Auth::guest()) {
            redirect('/');
        }

        return true;
    }
}
