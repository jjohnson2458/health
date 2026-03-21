<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Session;

class GuestMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (Session::has('user_id') && Session::get('auth_verified', false)) {
            header('Location: /dashboard');
            exit;
            return false;
        }
        return true;
    }
}
