<?php

use App\Controllers\AuthController;
use App\Controllers\ExportController;
use App\Controllers\LegalController;
use App\Controllers\MedicationController;
use App\Controllers\AdminController;
use App\Controllers\AppointmentController;
use App\Controllers\DashboardController;
use App\Controllers\EntryController;
use App\Controllers\AnalyticsController;
use App\Controllers\CalculatorController;
use App\Controllers\FoodTrackerController;
use App\Controllers\PlannerController;
use App\Controllers\GuideController;
use App\Controllers\LanguageController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CsrfMiddleware;

$auth = [AuthMiddleware::class, CsrfMiddleware::class];
$admin = [AdminMiddleware::class, CsrfMiddleware::class];
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
$router->get('/forgot-password', AuthController::class, 'showForgotPassword', [GuestMiddleware::class]);
$router->post('/forgot-password', AuthController::class, 'forgotPassword', $guest);
$router->get('/reset-password/{token}', AuthController::class, 'showResetPassword');
$router->post('/reset-password', AuthController::class, 'resetPassword', $csrf);
$router->get('/logout', AuthController::class, 'logout');

// Legal pages (public, no auth required)
$router->get('/terms', LegalController::class, 'termsOfService');
$router->get('/privacy', LegalController::class, 'privacyPolicy');
$router->get('/hipaa', LegalController::class, 'hipaaNotice');

// Language switch
$router->get('/lang/{lang}', LanguageController::class, 'switch');

// Authenticated routes
$router->get('/dashboard', DashboardController::class, 'index', $auth);

$router->get('/entries', EntryController::class, 'index', $auth);
$router->get('/export/csv', ExportController::class, 'csv', $auth);

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

$router->get('/calculator/food', FoodTrackerController::class, 'index', $auth);

$router->get('/planner', PlannerController::class, 'index', $auth);
$router->post('/planner', PlannerController::class, 'create', $auth);
$router->get('/planner/data', PlannerController::class, 'data', $auth);

$router->get('/guide', GuideController::class, 'index', $auth);

// Medications
$router->get('/medications', MedicationController::class, 'index', $auth);
$router->get('/medications/create', MedicationController::class, 'create', $auth);
$router->post('/medications', MedicationController::class, 'store', $auth);
$router->get('/medications/share', MedicationController::class, 'share', $auth);
$router->get('/medications/{id}', MedicationController::class, 'edit', $auth);
$router->post('/medications/{id}', MedicationController::class, 'update', $auth);
$router->post('/medications/{id}/discontinue', MedicationController::class, 'discontinue', $auth);
$router->post('/medications/{id}/reactivate', MedicationController::class, 'reactivate', $auth);
$router->get('/medications/{id}/history', MedicationController::class, 'history', $auth);

// Appointments
$router->get('/appointments', AppointmentController::class, 'index', $auth);
$router->get('/appointments/calendar', AppointmentController::class, 'calendar', $auth);
$router->get('/appointments/create', AppointmentController::class, 'create', $auth);
$router->post('/appointments', AppointmentController::class, 'store', $auth);
$router->get('/appointments/{id}', AppointmentController::class, 'edit', $auth);
$router->post('/appointments/{id}', AppointmentController::class, 'update', $auth);
$router->post('/appointments/{id}/complete', AppointmentController::class, 'complete', $auth);
$router->post('/appointments/{id}/cancel', AppointmentController::class, 'cancel', $auth);

// Admin routes
$router->get('/admin', AdminController::class, 'dashboard', $admin);
$router->get('/admin/users', AdminController::class, 'users', $admin);
$router->get('/admin/users/create', AdminController::class, 'createUser', $admin);
$router->post('/admin/users/create', AdminController::class, 'storeUser', $admin);
$router->get('/admin/users/{id}', AdminController::class, 'editUser', $admin);
$router->post('/admin/users/{id}', AdminController::class, 'updateUser', $admin);
$router->post('/admin/users/{id}/delete', AdminController::class, 'deleteUser', $admin);
$router->get('/admin/users/{id}/export', AdminController::class, 'exportUserData', $admin);
$router->get('/admin/errors', AdminController::class, 'errors', $admin);
