<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use App\Models\User;

class LanguageController extends Controller
{
    public function switch(string $lang): void
    {
        $allowed = ['en', 'es'];
        if (!in_array($lang, $allowed)) {
            $lang = 'en';
        }

        Session::set('lang', $lang);

        // Update user preference if logged in
        $userId = Session::get('user_id');
        if ($userId) {
            User::update((int) $userId, ['language' => $lang]);
            $userData = Session::get('user_data', []);
            $userData['language'] = $lang;
            Session::set('user_data', $userData);
        }

        $this->back();
    }
}
