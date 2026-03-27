<?php

/**
 * Sitemap Generator for VQ Healthy
 *
 * Reads the routes file and generates sitemap.xml containing only public routes.
 *
 * Usage: php scripts/generate-sitemap.php
 */

$baseUrl = 'https://vqhealthy.com';
$routesFile = __DIR__ . '/../config/routes.php';
$outputFile = __DIR__ . '/../public/sitemap.xml';

// Define which routes are public (no auth required)
// These are routes that use GuestMiddleware, no middleware, or are legal pages
$publicPatterns = [
    '/'                => ['changefreq' => 'monthly',  'priority' => '1.0'],
    '/login'           => ['changefreq' => 'monthly',  'priority' => '0.8'],
    '/register'        => ['changefreq' => 'monthly',  'priority' => '0.8'],
    '/terms'           => ['changefreq' => 'yearly',   'priority' => '0.5'],
    '/privacy'         => ['changefreq' => 'yearly',   'priority' => '0.5'],
    '/hipaa'           => ['changefreq' => 'yearly',   'priority' => '0.5'],
];

// Parse routes file to discover public GET routes
$routesContent = file_get_contents($routesFile);
$discoveredRoutes = [];

// Match GET routes that don't use $auth or $admin middleware
// Pattern: $router->get('/path', Controller::class, 'method') or with GuestMiddleware/no middleware
preg_match_all(
    '/\$router->get\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[^)]+\)/',
    $routesContent,
    $matches
);

foreach ($matches[1] as $route) {
    // Skip routes with parameters like {id} or {token}
    if (strpos($route, '{') !== false) {
        continue;
    }

    // Check if this route uses $auth or $admin middleware (authenticated only)
    $pattern = preg_quote($route, '/');
    if (preg_match('/\$router->get\(\s*[\'"]\/' . ltrim($pattern, '\\/') . '[\'"]\s*,[^)]*\$auth/', $routesContent)) {
        continue;
    }
    if (preg_match('/\$router->get\(\s*[\'"]\/' . ltrim($pattern, '\\/') . '[\'"]\s*,[^)]*\$admin/', $routesContent)) {
        continue;
    }

    // This is a public route - add it if not already in our list
    if (!isset($publicPatterns[$route])) {
        $publicPatterns[$route] = ['changefreq' => 'monthly', 'priority' => '0.3'];
    }
    $discoveredRoutes[] = $route;
}

// Generate XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

foreach ($publicPatterns as $path => $meta) {
    $loc = rtrim($baseUrl, '/') . $path;
    $xml .= '    <url>' . PHP_EOL;
    $xml .= '        <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
    $xml .= '        <changefreq>' . $meta['changefreq'] . '</changefreq>' . PHP_EOL;
    $xml .= '        <priority>' . $meta['priority'] . '</priority>' . PHP_EOL;
    $xml .= '    </url>' . PHP_EOL;
}

$xml .= '</urlset>' . PHP_EOL;

// Write to file
file_put_contents($outputFile, $xml);

echo "Sitemap generated: {$outputFile}" . PHP_EOL;
echo "Routes included: " . count($publicPatterns) . PHP_EOL;
foreach ($publicPatterns as $path => $meta) {
    echo "  {$path} (priority: {$meta['priority']})" . PHP_EOL;
}
