<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\EntryController;
use App\Controllers\AnalyticsController;
use App\Controllers\CalculatorController;
use App\Controllers\PlannerController;
use App\Controllers\GuideController;
use App\Controllers\LanguageController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CsrfMiddleware;

$auth = [AuthMiddleware::class, CsrfMiddleware::class];
$guest = [GuestMiddleware::class, CsrfMiddleware::class];
$csrf = [CsrfMiddleware::class];

// Public / Guest routes
$router->get('/', AuthController::class, 'showLogin', [GuestMiddleware::class]);
$router->get('/login', AuthController::class, 'showLogin', [GuestMiddleware::class]);
$router->post('/login', AuthController::class, 'login', $guest);
$router->get('/register', AuthController::class, 'showRegister', [GuestMiddleware::class]);
$router->post('/register', AuthController::class, 'register', $guest);
$router->get('/verify-email/{token}', AuthController::class, 'verifyEmail');
$router->get('/verify-code', AuthController::class, 'showVerifyCode');
$router->post('/verify-code', AuthController::class, 'verifyCode', $csrf);
$router->get('/resend-code', AuthController::class, 'resendCode');
$router->get('/logout', AuthController::class, 'logout');

// Language switch
$router->get('/lang/{lang}', LanguageController::class, 'switch');

// Authenticated routes
$router->get('/dashboard', DashboardController::class, 'index', $auth);

$router->get('/entry', EntryController::class, 'create', $auth);
$router->post('/entry', EntryController::class, 'store', $auth);
$router->get('/entry/{id}', EntryController::class, 'edit', $auth);
$router->post('/entry/{id}', EntryController::class, 'update', $auth);
$router->post('/entry/{id}/delete', EntryController::class, 'delete', $auth);

$router->get('/analytics', AnalyticsController::class, 'index', $auth);
$router->get('/analytics/data', AnalyticsController::class, 'data', $auth);

$router->get('/calculator/calories', CalculatorController::class, 'calories', $auth);
$router->post('/calculator/calories', CalculatorController::class, 'calculateCalories', $auth);
$router->get('/calculator/macros', CalculatorController::class, 'macros', $auth);
$router->post('/calculator/macros', CalculatorController::class, 'calculateMacros', $auth);

$router->get('/planner', PlannerController::class, 'index', $auth);
$router->post('/planner', PlannerController::class, 'create', $auth);
$router->get('/planner/data', PlannerController::class, 'data', $auth);

$router->get('/guide', GuideController::class, 'index', $auth);
