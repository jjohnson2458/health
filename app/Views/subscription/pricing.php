<?php if ($upgradeRequired): ?>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-star"></i> <?= e(__('subscription.upgrade_required')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="text-center mb-4">
    <h4><?= e(__('subscription.pricing_title')) ?></h4>
    <p class="text-muted"><?= e(__('subscription.pricing_subtitle')) ?></p>
</div>

<!-- Billing Toggle -->
<div class="text-center mb-4">
    <div class="btn-group" role="group" id="billingToggle">
        <button type="button" class="btn btn-outline-primary active" data-interval="monthly"><?= e(__('subscription.monthly')) ?></button>
        <button type="button" class="btn btn-outline-primary" data-interval="yearly">
            <?= e(__('subscription.yearly')) ?> <span class="badge bg-success ms-1"><?= e(__('subscription.save_20')) ?></span>
        </button>
    </div>
</div>

<div class="row g-4 justify-content-center mb-5">
    <!-- Free Tier -->
    <div class="col-md-4">
        <div class="card h-100 <?= $currentTier === 'free' ? 'border-primary' : '' ?>">
            <?php if ($currentTier === 'free'): ?>
            <div class="card-header bg-primary text-white text-center fw-bold"><?= e(__('subscription.current_plan')) ?></div>
            <?php endif; ?>
            <div class="card-body text-center">
                <h5 class="card-title"><?= e(__('subscription.tier_free')) ?></h5>
                <div class="display-5 fw-bold mb-2">$0</div>
                <p class="text-muted small mb-4"><?= e(__('subscription.forever_free')) ?></p>
                <ul class="list-unstyled text-start small">
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <?= e(__('subscription.feature_weight')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <?= e(__('subscription.feature_7day')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <?= e(__('subscription.feature_calculators')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <?= e(__('subscription.feature_food_basic')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <?= e(__('subscription.feature_bilingual')) ?></li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <?= e(__('subscription.feature_encryption')) ?></li>
                </ul>
            </div>
            <div class="card-footer text-center">
                <?php if ($currentTier === 'free'): ?>
                    <button class="btn btn-outline-secondary btn-sm w-100" disabled><?= e(__('subscription.current_plan')) ?></button>
                <?php else: ?>
                    <span class="text-muted small"><?= e(__('subscription.included')) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Premium Tier -->
    <div class="col-md-4">
        <div class="card h-100 border-warning <?= $currentTier === 'premium' ? 'border-primary' : '' ?>">
            <?php if ($currentTier === 'premium'): ?>
            <div class="card-header bg-primary text-white text-center fw-bold"><?= e(__('subscription.current_plan')) ?></div>
            <?php else: ?>
            <div class="card-header bg-warning text-dark text-center fw-bold"><?= e(__('subscription.most_popular')) ?></div>
            <?php endif; ?>
            <div class="card-body text-center">
                <h5 class="card-title"><?= e(__('subscription.tier_premium')) ?></h5>
                <div class="display-5 fw-bold mb-1">
                    <span class="price-monthly">$7.99</span>
                    <span class="price-yearly d-none">$4.99</span>
                </div>
                <p class="text-muted small mb-4">
                    <span class="price-monthly">/<?= e(__('subscription.month')) ?></span>
                    <span class="price-yearly d-none">/<?= e(__('subscription.month')) ?> ($59.99/<?= e(__('subscription.year')) ?>)</span>
                </p>
                <ul class="list-unstyled text-start small">
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <?= e(__('subscription.everything_free')) ?></li>
                    <li class="mb-2"><i class="bi bi-star-fill text-warning"></i> <?= e(__('subscription.feature_unlimited_food')) ?></li>
                    <li class="mb-2"><i class="bi bi-star-fill text-warning"></i> <?= e(__('subscription.feature_full_analytics')) ?></li>
                    <li class="mb-2"><i class="bi bi-star-fill text-warning"></i> <?= e(__('subscription.feature_planner')) ?></li>
                    <li class="mb-2"><i class="bi bi-star-fill text-warning"></i> <?= e(__('subscription.feature_medications')) ?></li>
                    <li class="mb-2"><i class="bi bi-star-fill text-warning"></i> <?= e(__('subscription.feature_appointments')) ?></li>
                    <li class="mb-2"><i class="bi bi-star-fill text-warning"></i> <?= e(__('subscription.feature_export')) ?></li>
                </ul>
            </div>
            <div class="card-footer text-center">
                <?php if ($currentTier === 'premium'): ?>
                    <a href="/subscription/portal" class="btn btn-outline-primary btn-sm w-100"><?= e(__('subscription.manage')) ?></a>
                <?php elseif ($currentTier === 'free'): ?>
                    <form method="POST" action="/subscription/checkout" class="checkout-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="tier" value="premium">
                        <input type="hidden" name="interval" class="interval-input" value="monthly">
                        <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold"><?= e(__('subscription.upgrade')) ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Premium+ Tier -->
    <div class="col-md-4">
        <div class="card h-100 <?= $currentTier === 'premium_plus' ? 'border-primary' : '' ?>">
            <?php if ($currentTier === 'premium_plus'): ?>
            <div class="card-header bg-primary text-white text-center fw-bold"><?= e(__('subscription.current_plan')) ?></div>
            <?php else: ?>
            <div class="card-header bg-dark text-white text-center fw-bold"><?= e(__('subscription.tier_premium_plus')) ?></div>
            <?php endif; ?>
            <div class="card-body text-center">
                <h5 class="card-title"><?= e(__('subscription.tier_premium_plus')) ?></h5>
                <div class="display-5 fw-bold mb-1">
                    <span class="price-monthly">$14.99</span>
                    <span class="price-yearly d-none">$9.99</span>
                </div>
                <p class="text-muted small mb-4">
                    <span class="price-monthly">/<?= e(__('subscription.month')) ?></span>
                    <span class="price-yearly d-none">/<?= e(__('subscription.month')) ?> ($119.99/<?= e(__('subscription.year')) ?>)</span>
                </p>
                <ul class="list-unstyled text-start small">
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> <?= e(__('subscription.everything_premium')) ?></li>
                    <li class="mb-2"><i class="bi bi-gem text-info"></i> <?= e(__('subscription.feature_cgm')) ?></li>
                    <li class="mb-2"><i class="bi bi-gem text-info"></i> <?= e(__('subscription.feature_provider_push')) ?></li>
                    <li class="mb-2"><i class="bi bi-gem text-info"></i> <?= e(__('subscription.feature_diabetes_analytics')) ?></li>
                    <li class="mb-2"><i class="bi bi-gem text-info"></i> <?= e(__('subscription.feature_caregiver')) ?></li>
                </ul>
            </div>
            <div class="card-footer text-center">
                <?php if ($currentTier === 'premium_plus'): ?>
                    <a href="/subscription/portal" class="btn btn-outline-primary btn-sm w-100"><?= e(__('subscription.manage')) ?></a>
                <?php else: ?>
                    <form method="POST" action="/subscription/checkout" class="checkout-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="tier" value="premium_plus">
                        <input type="hidden" name="interval" class="interval-input" value="monthly">
                        <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold"><?= e(__('subscription.upgrade')) ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Affiliate Partners Section -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-heart"></i> <?= e(__('subscription.partners_title')) ?>
    </div>
    <div class="card-body">
        <p class="small text-muted mb-3"><?= e(__('subscription.partners_subtitle')) ?></p>
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3">
                <a href="/affiliate/noom" class="text-decoration-none" target="_blank" rel="noopener">
                    <div class="card border-0 bg-light p-3">
                        <i class="bi bi-app text-success fs-3"></i>
                        <div class="small mt-1 text-dark fw-semibold">Noom</div>
                        <div class="text-muted" style="font-size: 0.7rem;"><?= e(__('subscription.affiliate_weight_loss')) ?></div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="/affiliate/myfitnesspal" class="text-decoration-none" target="_blank" rel="noopener">
                    <div class="card border-0 bg-light p-3">
                        <i class="bi bi-journal-check text-primary fs-3"></i>
                        <div class="small mt-1 text-dark fw-semibold">MyFitnessPal</div>
                        <div class="text-muted" style="font-size: 0.7rem;"><?= e(__('subscription.affiliate_calorie')) ?></div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="/affiliate/nutrisense" class="text-decoration-none" target="_blank" rel="noopener">
                    <div class="card border-0 bg-light p-3">
                        <i class="bi bi-activity text-danger fs-3"></i>
                        <div class="small mt-1 text-dark fw-semibold">Nutrisense</div>
                        <div class="text-muted" style="font-size: 0.7rem;"><?= e(__('subscription.affiliate_cgm')) ?></div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="/affiliate/amazon-health" class="text-decoration-none" target="_blank" rel="noopener">
                    <div class="card border-0 bg-light p-3">
                        <i class="bi bi-box-seam text-warning fs-3"></i>
                        <div class="small mt-1 text-dark fw-semibold">Amazon Health</div>
                        <div class="text-muted" style="font-size: 0.7rem;"><?= e(__('subscription.affiliate_supplements')) ?></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FAQ -->
<div class="card mb-4">
    <div class="card-header"><?= e(__('subscription.faq_title')) ?></div>
    <div class="card-body">
        <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed small" type="button" data-bs-toggle="collapse" data-bs-target="#faq1"><?= e(__('subscription.faq_cancel_q')) ?></button></h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body small"><?= e(__('subscription.faq_cancel_a')) ?></div></div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed small" type="button" data-bs-toggle="collapse" data-bs-target="#faq2"><?= e(__('subscription.faq_hipaa_q')) ?></button></h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body small"><?= e(__('subscription.faq_hipaa_a')) ?></div></div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed small" type="button" data-bs-toggle="collapse" data-bs-target="#faq3"><?= e(__('subscription.faq_data_q')) ?></button></h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body small"><?= e(__('subscription.faq_data_a')) ?></div></div>
            </div>
        </div>
    </div>
</div>

<?php $scripts = '<script>
document.querySelectorAll("#billingToggle button").forEach(btn => {
    btn.addEventListener("click", function() {
        document.querySelectorAll("#billingToggle button").forEach(b => b.classList.remove("active"));
        this.classList.add("active");
        const interval = this.dataset.interval;
        document.querySelectorAll(".price-monthly").forEach(el => el.classList.toggle("d-none", interval === "yearly"));
        document.querySelectorAll(".price-yearly").forEach(el => el.classList.toggle("d-none", interval === "monthly"));
        document.querySelectorAll(".interval-input").forEach(el => el.value = interval);
    });
});
</script>'; ?>
