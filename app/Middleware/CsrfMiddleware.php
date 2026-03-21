<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Session;

class CsrfMiddleware extends Middleware
{
    public function handle(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf_token'] ?? '';
            if (!Session::verifyCsrfToken($token)) {
                http_response_code(403);
                echo 'Invalid CSRF token.';
                exit;
                return false;
            }
        }
        return true;
    }
}
