<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Session;

class PremiumPlusMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!hasTier('premium_plus')) {
            Session::flash('upgrade_required', true);
            header('Location: /pricing');
            exit;
            return false;
        }
        return true;
    }
}
