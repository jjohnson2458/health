<div class="row justify-content-center">
    <div class="auth-card">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-heart-pulse text-primary" style="font-size:2.5rem;"></i>
                    <h3 class="mt-2"><?= e(__('app_name')) ?></h3>
                </div>

                <h5 class="text-center mb-3"><?= e(__('auth.register')) ?></h5>

                <form method="POST" action="/register">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="first_name" class="form-label"><?= e(__('auth.first_name')) ?></label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?= e($old['first_name'] ?? '') ?>" required autofocus>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="last_name" class="form-label"><?= e(__('auth.last_name')) ?></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?= e($old['last_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><?= e(__('auth.email')) ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= e($old['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label"><?= e(__('auth.password')) ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                   minlength="8" required>
                        </div>
                        <div class="form-text">Minimum 8 characters</div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label"><?= e(__('auth.confirm_password')) ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                   minlength="8" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-person-plus"></i> <?= e(__('auth.register')) ?>
                    </button>
                </form>

                <p class="text-center small mb-0">
                    <?= e(__('auth.has_account')) ?>
                    <a href="/login"><?= e(__('auth.login')) ?></a>
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
