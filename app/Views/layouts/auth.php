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
    <!-- HIPAA Notice Banner -->
    <div class="bg-success bg-opacity-10 border-bottom border-success py-1 text-center small">
        <i class="bi bi-shield-lock text-success"></i>
        <span class="text-success fw-semibold"><?= e(__('hipaa_notice')) ?></span>
    </div>

    <!-- Language Switcher -->
    <div class="position-absolute top-0 end-0 mt-2 me-3">
        <div class="dropdown">
            <a class="btn btn-sm btn-outline-secondary dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-translate"></i> <?= e(__('language')) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/lang/en">English</a></li>
                <li><a class="dropdown-item" href="/lang/es">Español</a></li>
            </ul>
        </div>
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
    <main class="d-flex align-items-center min-vh-100">
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <footer class="text-center small py-3">
        <a href="/terms" class="text-muted text-decoration-none"><?= e(__('legal.terms')) ?></a>
        <span class="text-muted mx-1">|</span>
        <a href="/privacy" class="text-muted text-decoration-none"><?= e(__('legal.privacy')) ?></a>
        <span class="text-muted mx-1">|</span>
        <a href="/hipaa" class="text-muted text-decoration-none"><?= e(__('legal.hipaa')) ?></a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
