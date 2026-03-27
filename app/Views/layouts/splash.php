<!DOCTYPE html>
<html lang="<?= e(\Core\Session::get('lang', 'en')) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e(__('splash.meta_description')) ?>">
    <title><?= e(__('splash.page_title')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#198754">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="VQ Healthy">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.svg">
    <style>
        html { font-size: 14px; }
        .splash-hero { position: relative; }
        .splash-hero-img {
            width: 100%; height: auto; display: block;
        }
        .splash-hero-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,.6);
        }
        .splash-hero-content {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            display: flex; align-items: flex-start; justify-content: center;
            padding-top: 15%;
        }
        .splash-nav { background: #198754; }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark splash-nav">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-heart-pulse"></i> VQ Healthy
            </a>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <a class="btn btn-sm btn-outline-light dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-translate"></i> <?= e(__('language')) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/lang/en">English</a></li>
                        <li><a class="dropdown-item" href="/lang/es">Español</a></li>
                    </ul>
                </div>
                <a href="/login" class="btn btn-sm btn-outline-light"><?= e(__('auth.login')) ?></a>
                <a href="/register" class="btn btn-sm btn-light fw-semibold"><?= e(__('auth.register')) ?></a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <?= $content ?>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container text-center small">
            <p class="mb-2">
                <i class="bi bi-shield-lock text-success"></i>
                <span class="text-success fw-semibold"><?= e(__('hipaa_notice')) ?></span>
            </p>
            <p class="mb-2">&copy; <?= date('Y') ?> VQ Healthy. <?= e(__('splash.rights')) ?></p>
            <p class="mb-0">
                <a href="/terms" class="text-muted text-decoration-none"><?= e(__('legal.terms')) ?></a>
                <span class="text-muted mx-1">|</span>
                <a href="/privacy" class="text-muted text-decoration-none"><?= e(__('legal.privacy')) ?></a>
                <span class="text-muted mx-1">|</span>
                <a href="/hipaa" class="text-muted text-decoration-none"><?= e(__('legal.hipaa')) ?></a>
                <span class="text-muted mx-1">|</span>
                <a href="/pricing" class="text-muted text-decoration-none"><?= e(__('splash.pricing_link')) ?></a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(reg) { console.log('[PWA] SW registered, scope:', reg.scope); })
                .catch(function(err) { console.error('[PWA] SW failed:', err); });
        });
    }
    </script>
</body>
</html>
