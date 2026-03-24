<div class="row justify-content-center">
    <div class="auth-card">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-lock-fill text-primary" style="font-size:2.5rem;"></i>
                    <h3 class="mt-2"><?= e(__('app_name')) ?></h3>
                </div>

                <h5 class="text-center mb-3"><?= e(__('auth.reset_password')) ?></h5>

                <form method="POST" action="/reset-password">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= e($token ?? '') ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label"><?= e(__('auth.new_password')) ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                   minlength="8" required autofocus>
                        </div>
                        <div class="form-text"><?= e(__('validation.min', ['min' => 8])) ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label"><?= e(__('auth.confirm_password')) ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                   minlength="8" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-check-circle"></i> <?= e(__('auth.reset_password')) ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            <span class="badge bg-success hipaa-badge">
                <i class="bi bi-shield-lock"></i> <?= e(__('hipaa_notice')) ?>
            </span>
        </div>
    </div>
</div>
