<?php

use Core\Session;

/**
 * Translate a key using the current language.
 */
function __(string $key, array $replace = []): string
{
    static $translations = null;
    static $loadedLang = null;

    $lang = Session::get('lang', $_ENV['APP_LANG'] ?? 'en');

    if ($translations === null || $loadedLang !== $lang) {
        $file = dirname(__DIR__) . '/Lang/' . $lang . '.php';
        $translations = file_exists($file) ? require $file : [];
        $loadedLang = $lang;
    }

    // Support nested keys like 'validation.required'
    $value = $translations;
    foreach (explode('.', $key) as $segment) {
        if (is_array($value) && isset($value[$segment])) {
            $value = $value[$segment];
        } else {
            return $key; // Return key if translation missing
        }
    }

    if (!is_string($value)) {
        return $key;
    }

    foreach ($replace as $placeholder => $replacement) {
        $value = str_replace(':' . $placeholder, $replacement, $value);
    }

    return $value;
}

/**
 * Escape HTML output.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a URL for the application.
 */
function url(string $path = ''): string
{
    $base = rtrim($_ENV['APP_URL'] ?? '', '/');
    return $base . '/' . ltrim($path, '/');
}

/**
 * Get old form input value.
 */
function old(string $key, string $default = ''): string
{
    $old = Session::getFlash('_old_input', []);
    return $old[$key] ?? $default;
}

/**
 * Check if user is authenticated.
 */
function auth(): bool
{
    return Session::has('user_id') && Session::get('auth_verified', false);
}

/**
 * Get the authenticated user's ID.
 */
function authId(): ?int
{
    return auth() ? (int) Session::get('user_id') : null;
}

/**
 * CSRF hidden input field.
 */
function csrf_field(): string
{
    $token = Session::generateCsrfToken();
    return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
}

/**
 * Format a date for display.
 */
function formatDate(string $date, string $format = 'M j, Y'): string
{
    return date($format, strtotime($date));
}

/**
 * Get the current user's subscription tier.
 */
function userTier(): string
{
    $userData = Session::get('user_data', []);
    return $userData['subscription_tier'] ?? 'free';
}

/**
 * Check if user has at least the given tier.
 */
function hasTier(string $requiredTier): bool
{
    $tiers = ['free' => 0, 'premium' => 1, 'premium_plus' => 2];
    $current = $tiers[userTier()] ?? 0;
    $required = $tiers[$requiredTier] ?? 0;
    return $current >= $required;
}

/**
 * Check if user is on premium or above.
 */
function isPremium(): bool
{
    return hasTier('premium');
}

/**
 * Check if user is on premium plus.
 */
function isPremiumPlus(): bool
{
    return hasTier('premium_plus');
}
