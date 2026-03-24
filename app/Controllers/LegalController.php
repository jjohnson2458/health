<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class LegalController extends Controller
{
    public function termsOfService(): void
    {
        View::render('legal/terms', ['title' => __('legal.terms_title')], 'layouts/auth');
    }

    public function privacyPolicy(): void
    {
        View::render('legal/privacy', ['title' => __('legal.privacy_title')], 'layouts/auth');
    }

    public function hipaaNotice(): void
    {
        View::render('legal/hipaa', ['title' => __('legal.hipaa_title')], 'layouts/auth');
    }
}
