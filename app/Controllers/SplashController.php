<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class SplashController extends Controller
{
    public function index(): void
    {
        View::render('splash/index', [
            'title' => __('splash.page_title'),
        ], 'layouts/splash');
    }
}
