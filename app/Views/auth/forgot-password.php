<div class="row justify-content-center">
    <div class="auth-card">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-key text-primary" style="font-size:2.5rem;"></i>
                    <h3 class="mt-2"><?= e(__('app_name')) ?></h3>
                </div>

                <h5 class="text-center mb-3"><?= e(__('auth.forgot_password')) ?></h5>
                <p class="text-muted small text-center mb-3"><?= e(__('auth.forgot_password_desc')) ?></p>

                <form method="POST" action="/forgot-password">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label"><?= e(__('auth.email')) ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= e($old['email'] ?? '') ?>" required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-send"></i> <?= e(__('auth.send_reset_link')) ?>
                    </button>
                </form>

                <p class="text-center small mb-0">
                    <a href="/login"><i class="bi bi-arrow-left"></i> <?= e(__('auth.back_to_login')) ?></a>
                </p>
            </div>
        </div>

        <div class="text-center mt-3">
            <span class="badge bg-success hipaa-badge">
                <i class="bi bi-shield-lock"></i> <?= e(__('hipaa_notice')) ?>
            </span>
        </div>
    </div>
</div>
