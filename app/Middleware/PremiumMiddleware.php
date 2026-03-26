<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Session;

class PremiumMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!hasTier('premium')) {
            Session::flash('upgrade_required', true);
            header('Location: /pricing');
            exit;
            return false;
        }
        return true;
    }
}
