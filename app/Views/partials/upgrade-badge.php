<?php if (!isPremium()): ?>
<a href="/pricing" class="badge bg-warning text-dark text-decoration-none ms-1" title="<?= e(__('subscription.premium_feature')) ?>">
    <i class="bi bi-star-fill"></i> PRO
</a>
<?php endif; ?>
