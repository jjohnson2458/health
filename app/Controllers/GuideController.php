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
}
