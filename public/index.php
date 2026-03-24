<?php

/**
 * Claude Health - Front Controller
 * All requests are routed through this file.
 */

// Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Load helper functions
require_once __DIR__ . '/../app/Helpers/functions.php';

// Set timezone
date_default_timezone_set('America/New_York');

// Start session
\Core\Session::start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; font-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:; connect-src 'self'");

// Global error and exception handlers
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    $levels = [E_WARNING => 'WARNING', E_NOTICE => 'NOTICE', E_USER_ERROR => 'ERROR', E_USER_WARNING => 'WARNING', E_USER_NOTICE => 'NOTICE'];
    $level = $levels[$errno] ?? 'ERROR';
    \App\Models\ErrorLog::capture($level, $errstr, $errfile, $errline);
    return false;
});

set_exception_handler(function (\Throwable $e): void {
    \App\Models\ErrorLog::capture(
        'EXCEPTION',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    if (($_ENV['APP_ENV'] ?? 'production') !== 'local') {
        http_response_code(500);
        include __DIR__ . '/../app/Views/errors/500.php';
        exit;
    }
    throw $e;
});

// Initialize router
$router = new \Core\Router();

// Load routes
require_once __DIR__ . '/../config/routes.php';

// Dispatch the request
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);
