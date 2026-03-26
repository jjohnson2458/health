<!DOCTYPE html>
<html lang="<?= e(\Core\Session::get('lang', 'en')) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? __('app_name')) ?> - <?= e(__('app_name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
    <style>
        html { font-size: 14px; }
    </style>
</head>
<body class="bg-light">
    <?php if (auth()): ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/dashboard">
                <i class="bi bi-heart-pulse"></i> <?= e(__('app_name')) ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2"></i> <?= e(__('nav.dashboard')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/entry"><i class="bi bi-journal-plus"></i> <?= e(__('nav.entries')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/analytics"><i class="bi bi-graph-up"></i> <?= e(__('nav.analytics')) ?></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-calculator"></i> <?= e(__('nav.calculators')) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/calculator/calories"><?= e(__('nav.calorie_calc')) ?></a></li>
                            <li><a class="dropdown-item" href="/calculator/macros"><?= e(__('nav.macro_calc')) ?></a></li>
                            <li><a class="dropdown-item" href="/calculator/food"><?= e(__('nav.food_tracker')) ?></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/planner"><i class="bi bi-calendar-check"></i> <?= e(__('nav.planner')) ?><?php if (!isPremium()): ?> <span class="badge bg-warning text-dark" style="font-size:0.6rem">PRO</span><?php endif; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/medications"><i class="bi bi-capsule"></i> <?= e(__('nav.medications')) ?><?php if (!isPremium()): ?> <span class="badge bg-warning text-dark" style="font-size:0.6rem">PRO</span><?php endif; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/appointments"><i class="bi bi-calendar-event"></i> <?= e(__('nav.appointments')) ?><?php if (!isPremium()): ?> <span class="badge bg-warning text-dark" style="font-size:0.6rem">PRO</span><?php endif; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/guide"><i class="bi bi-book"></i> <?= e(__('nav.guide')) ?></a>
                    </li>
                    <?php $userData = \Core\Session::get('user_data', []); ?>
                    <?php if (($userData['role'] ?? '') === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="/admin"><i class="bi bi-shield-lock"></i> Admin</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (!isPremium()): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="/pricing"><i class="bi bi-star-fill"></i> <?= e(__('nav.upgrade')) ?></a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/subscription/portal"><i class="bi bi-credit-card"></i> <?= e(__('nav.billing')) ?></a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-translate"></i> <?= e(__('language')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/lang/en">English</a></li>
                            <li><a class="dropdown-item" href="/lang/es">Español</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">
                            <i class="bi bi-box-arrow-right"></i> <?= e(__('logout')) ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- HIPAA Notice Banner -->
    <div class="bg-success bg-opacity-10 border-bottom border-success py-1 text-center small">
        <i class="bi bi-shield-lock text-success"></i>
        <span class="text-success fw-semibold"><?= e(__('hipaa_notice')) ?></span>
    </div>

    <!-- Flash Messages -->
    <div class="container mt-3">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($errors) && is_array($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif (!empty($errors) && is_string($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= e($errors) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <main class="container py-4">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-3 mt-auto">
        <div class="container text-center small">
            <p class="mb-1">&copy; <?= date('Y') ?> <?= e(__('app_name')) ?>. <?= e(__('hipaa_notice')) ?></p>
            <p class="mb-1">
                <a href="/terms" class="text-muted text-decoration-none"><?= e(__('legal.terms')) ?></a>
                <span class="text-muted mx-1">|</span>
                <a href="/privacy" class="text-muted text-decoration-none"><?= e(__('legal.privacy')) ?></a>
                <span class="text-muted mx-1">|</span>
                <a href="/hipaa" class="text-muted text-decoration-none"><?= e(__('legal.hipaa')) ?></a>
            </p>
            <p class="mb-0 text-muted"><?= e(__('tagline')) ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="/js/app.js"></script>
    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>
</body>
</html>
