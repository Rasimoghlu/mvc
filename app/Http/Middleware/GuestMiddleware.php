<?php

namespace App\Http\Middleware;

use App\Interfaces\MiddlewareInterface;
use Src\Facades\Auth;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        if (Auth::check()) {
            redirect('/tasks');
        }

        return true;
    }
}
