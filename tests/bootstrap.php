<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load test environment
$envFile = __DIR__ . '/../.env.testing';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__), '.env.testing');
    $dotenv->load();
} else {
    // Fallback: use main .env but override for testing
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

// Load helper functions
require_once __DIR__ . '/../app/Helpers/functions.php';

// Set timezone
date_default_timezone_set('America/New_York');
