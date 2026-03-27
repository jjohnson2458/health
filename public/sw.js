/**
 * VQ Healthy - Service Worker
 * Provides offline caching and network-first data strategy.
 */

const CACHE_VERSION = 'vq-healthy-v1';
const STATIC_CACHE = CACHE_VERSION + '-static';
const DYNAMIC_CACHE = CACHE_VERSION + '-dynamic';

// App shell resources to pre-cache on install
const APP_SHELL = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    '/icons/icon-192x192.svg',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js'
];

// Routes that should always try network first (data/API endpoints)
const NETWORK_FIRST_PATTERNS = [
    /\/api\//,
    /\/login/,
    /\/logout/,
    /\/register/,
    /\/verify/,
    /\/lang\//,
    /\/entry\/save/,
    /\/entry\/delete/,
    /\/csrf-token/
];

// Install: pre-cache the app shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('[SW] Pre-caching app shell');
                return cache.addAll(APP_SHELL);
            })
            .then(() => self.skipWaiting())
            .catch((err) => {
                console.error('[SW] Pre-cache failed:', err);
            })
    );
});

// Activate: clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => {
                return Promise.all(
                    keys.filter((key) => {
                        return key !== STATIC_CACHE && key !== DYNAMIC_CACHE;
                    }).map((key) => {
                        console.log('[SW] Removing old cache:', key);
                        return caches.delete(key);
                    })
                );
            })
            .then(() => self.clients.claim())
    );
});

// Fetch: route requests through appropriate strategy
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Skip non-GET requests (form submissions, etc.)
    if (request.method !== 'GET') {
        return;
    }

    // Network-first for data/API routes and auth flows
    if (NETWORK_FIRST_PATTERNS.some((pattern) => pattern.test(request.url))) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Cache-first for static assets (CSS, JS, images, fonts)
    if (isStaticAsset(request.url)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Network-first for HTML pages (so content stays fresh, but works offline)
    event.respondWith(networkFirst(request));
});

/**
 * Cache-first strategy: serve from cache, fall back to network.
 * Used for static assets that rarely change.
 */
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) {
        return cached;
    }

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        return offlineFallback();
    }
}

/**
 * Network-first strategy: try network, fall back to cache.
 * Used for HTML pages and data requests.
 */
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok && request.url.indexOf('chrome-extension') === -1) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        const cached = await caches.match(request);
        if (cached) {
            return cached;
        }
        // Only show offline fallback for navigation requests (HTML pages)
        if (request.headers.get('accept') && request.headers.get('accept').includes('text/html')) {
            return offlineFallback();
        }
        return new Response('', { status: 408, statusText: 'Offline' });
    }
}

/**
 * Check if a URL points to a static asset.
 */
function isStaticAsset(url) {
    return /\.(css|js|svg|png|jpg|jpeg|gif|ico|woff2?|ttf|eot)(\?.*)?$/.test(url) ||
           url.includes('cdn.jsdelivr.net');
}

/**
 * Generate an offline fallback page.
 */
function offlineFallback() {
    const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - VQ Healthy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>html { font-size: 14px; }</style>
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container text-center py-5">
        <i class="bi bi-wifi-off text-muted" style="font-size:4rem"></i>
        <h2 class="mt-3">You're Offline</h2>
        <p class="text-muted">VQ Healthy requires an internet connection for this page.<br>
        Previously visited pages may still be available.</p>
        <button class="btn btn-primary mt-2" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Try Again
        </button>
    </div>
</body>
</html>`;
    return new Response(html, {
        status: 200,
        headers: { 'Content-Type': 'text/html; charset=utf-8' }
    });
}
