<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Session;
use App\Models\User;

class AdminMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!Session::has('user_id') || !Session::get('auth_verified', false)) {
            Session::flash('errors', ['Please log in.']);
            header('Location: /login');
            exit;
            return false;
        }

        $user = User::find((int) Session::get('user_id'));
        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            Session::flash('errors', ['Access denied.']);
            header('Location: /dashboard');
            exit;
            return false;
        }

        return true;
    }
}
