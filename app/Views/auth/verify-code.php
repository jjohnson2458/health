<div class="row justify-content-center">
    <div class="auth-card">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-check text-primary" style="font-size:2.5rem;"></i>
                    <h4 class="mt-2"><?= e(__('auth.verify_code')) ?></h4>
                    <p class="text-muted small"><?= e(__('auth.verify_code_sent')) ?></p>
                </div>

                <form method="POST" action="/verify-code">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="code" class="form-label"><?= e(__('auth.enter_code')) ?></label>
                        <input type="text" class="form-control form-control-lg text-center"
                               id="code" name="code"
                               maxlength="6" pattern="[0-9]{6}"
                               placeholder="000000"
                               style="letter-spacing: 8px; font-size: 1.5rem;"
                               required autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-check-circle"></i> <?= e(__('auth.verify')) ?>
                    </button>
                </form>

                <div class="text-center">
                    <a href="/resend-code" class="small"><?= e(__('auth.resend_code')) ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
