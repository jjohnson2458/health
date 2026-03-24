<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class GuideController extends Controller
{
    public function index(): void
    {
        View::render('guide/index', ['title' => __('nav.guide')]);
    }

    public function features(): void
    {
        $path = dirname(__DIR__, 2) . '/docs/FEATURES.html';
        if (file_exists($path)) {
            header('Content-Type: text/html; charset=UTF-8');
            readfile($path);
            exit;
        }
        $this->redirect('/guide');
    }
}
