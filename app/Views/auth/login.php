<div class="row justify-content-center">
    <div class="auth-card">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-heart-pulse text-primary" style="font-size:2.5rem;"></i>
                    <h3 class="mt-2"><?= e(__('app_name')) ?></h3>
                    <p class="text-muted small"><?= e(__('tagline')) ?></p>
                </div>

                <h5 class="text-center mb-3"><?= e(__('auth.login')) ?></h5>

                <form method="POST" action="/login">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label"><?= e(__('auth.email')) ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= e($old['email'] ?? '') ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label"><?= e(__('auth.password')) ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <a href="/forgot-password" class="small text-muted"><?= e(__('auth.forgot_password')) ?></a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right"></i> <?= e(__('auth.login')) ?>
                    </button>
                </form>

                <?php $regEnabled = !isset($_ENV['NEW_USER_REGISTRATION']) || $_ENV['NEW_USER_REGISTRATION'] !== 'false'; ?>
                <?php if ($regEnabled): ?>
                <p class="text-center small mb-0">
                    <?= e(__('auth.no_account')) ?>
                    <a href="/register"><?= e(__('auth.register')) ?></a>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-3">
            <span class="badge bg-success hipaa-badge">
                <i class="bi bi-shield-lock"></i> <?= e(__('hipaa_notice')) ?>
            </span>
        </div>
    </div>
</div>
