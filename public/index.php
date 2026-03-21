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

// Initialize router
$router = new \Core\Router();

// Load routes
require_once __DIR__ . '/../config/routes.php';

// Dispatch the request
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);
