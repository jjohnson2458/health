<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use App\Models\User;

class UnitController extends Controller
{
    public function toggle(): void
    {
        $current = Session::get('unit_system', 'us');
        $newSystem = $current === 'us' ? 'metric' : 'us';

        Session::set('unit_system', $newSystem);

        // Persist to database if logged in
        $userId = Session::get('user_id');
        if ($userId) {
            User::update((int) $userId, ['unit_system' => $newSystem]);
            $userData = Session::get('user_data', []);
            $userData['unit_system'] = $newSystem;
            Session::set('user_data', $userData);
        }

        $this->back();
    }
}
