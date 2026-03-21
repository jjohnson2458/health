<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Session;

class AuthMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!Session::has('user_id') || !Session::get('auth_verified', false)) {
            Session::flash('error', __('auth.login_required'));
            header('Location: /login');
            exit;
            return false;
        }
        return true;
    }
}
