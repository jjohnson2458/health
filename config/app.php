<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Claude Health',
    'env' => $_ENV['APP_ENV'] ?? 'local',
    'url' => $_ENV['APP_URL'] ?? 'http://health.local',
    'timezone' => 'America/New_York',
    'default_lang' => 'en',
    'session_lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 30),
    'rate_limit' => [
        'max_attempts' => 5,
        'lockout_minutes' => 15,
    ],
    'twofa' => [
        'code_length' => 6,
        'expiry_minutes' => 10,
    ],
];
