<!-- Hero Section -->
<section class="splash-hero position-relative overflow-hidden">
    <div class="splash-hero-bg" style="background-image: url('/assets/img/hero-banner.png');"></div>
    <div class="splash-hero-overlay"></div>
    <div class="container position-relative" style="z-index:2;">
        <div class="row min-vh-75 align-items-center">
            <div class="col-lg-8 mx-auto text-center text-white py-5">
                <p class="text-uppercase fw-bold letter-spacing-2 mb-3 small" style="letter-spacing:.25em; opacity:.85;">VQ Healthy</p>
                <h1 class="display-4 fw-bold mb-3" style="font-family: 'Georgia', serif;"><?= e(__('splash.headline')) ?></h1>
                <p class="lead mb-4 mx-auto" style="max-width:600px; opacity:.9;"><?= e(__('splash.subheadline')) ?></p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="/register" class="btn btn-light btn-lg px-4 fw-semibold">
                        <i class="bi bi-person-plus"></i> <?= e(__('splash.cta_register')) ?>
                    </a>
                    <a href="/login" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right"></i> <?= e(__('splash.cta_login')) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Who We Are / About Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-3"><?= e(__('splash.about_title')) ?></h2>
                <p class="text-muted"><?= e(__('splash.about_p1')) ?></p>
                <p class="text-muted"><?= e(__('splash.about_p2')) ?></p>
                <div class="d-flex gap-4 mt-4">
                    <div class="text-center">
                        <div class="display-6 fw-bold text-primary">100%</div>
                        <div class="small text-muted"><?= e(__('splash.stat_hipaa')) ?></div>
                    </div>
                    <div class="text-center">
                        <div class="display-6 fw-bold text-success">AES-256</div>
                        <div class="small text-muted"><?= e(__('splash.stat_encryption')) ?></div>
                    </div>
                    <div class="text-center">
                        <div class="display-6 fw-bold text-info">2FA</div>
                        <div class="small text-muted"><?= e(__('splash.stat_auth')) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card border-0 bg-primary bg-opacity-10 p-3 text-center">
                            <i class="bi bi-graph-up-arrow text-primary" style="font-size:2rem;"></i>
                            <div class="fw-semibold mt-2 small"><?= e(__('splash.feature_tracking')) ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 bg-success bg-opacity-10 p-3 text-center">
                            <i class="bi bi-capsule text-success" style="font-size:2rem;"></i>
                            <div class="fw-semibold mt-2 small"><?= e(__('splash.feature_medications')) ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 bg-info bg-opacity-10 p-3 text-center">
                            <i class="bi bi-calculator text-info" style="font-size:2rem;"></i>
                            <div class="fw-semibold mt-2 small"><?= e(__('splash.feature_calculators')) ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 bg-warning bg-opacity-10 p-3 text-center">
                            <i class="bi bi-calendar-check text-warning" style="font-size:2rem;"></i>
                            <div class="fw-semibold mt-2 small"><?= e(__('splash.feature_planner')) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-2"><?= e(__('splash.features_title')) ?></h2>
        <p class="text-center text-muted mb-5"><?= e(__('splash.features_subtitle')) ?></p>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="mb-3"><i class="bi bi-graph-up-arrow text-primary" style="font-size:2.5rem;"></i></div>
                    <h5 class="fw-bold"><?= e(__('splash.feature_tracking')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('splash.feature_tracking_desc')) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="mb-3"><i class="bi bi-shield-lock text-success" style="font-size:2.5rem;"></i></div>
                    <h5 class="fw-bold"><?= e(__('splash.feature_hipaa')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('splash.feature_hipaa_desc')) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="mb-3"><i class="bi bi-capsule text-danger" style="font-size:2.5rem;"></i></div>
                    <h5 class="fw-bold"><?= e(__('splash.feature_medications')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('splash.feature_medications_desc')) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="mb-3"><i class="bi bi-calculator text-info" style="font-size:2.5rem;"></i></div>
                    <h5 class="fw-bold"><?= e(__('splash.feature_calculators')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('splash.feature_calculators_desc')) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="mb-3"><i class="bi bi-translate text-warning" style="font-size:2.5rem;"></i></div>
                    <h5 class="fw-bold"><?= e(__('splash.feature_bilingual')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('splash.feature_bilingual_desc')) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <div class="mb-3"><i class="bi bi-heart-pulse text-primary" style="font-size:2.5rem;"></i></div>
                    <h5 class="fw-bold"><?= e(__('splash.feature_vitals')) ?></h5>
                    <p class="text-muted small mb-0"><?= e(__('splash.feature_vitals_desc')) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Tiers Section -->
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-2"><?= e(__('splash.pricing_title')) ?></h2>
        <p class="text-center text-muted mb-5"><?= e(__('splash.pricing_subtitle')) ?></p>
        <div class="row g-4 justify-content-center">
            <!-- Free Tier -->
            <div class="col-md-4">
                <div class="card h-100 border shadow-sm">
                    <div class="card-header bg-light text-center py-3">
                        <h5 class="fw-bold mb-1"><?= e(__('subscription.tier_free')) ?></h5>
                        <div class="display-5 fw-bold text-primary">$0</div>
                        <div class="small text-muted"><?= e(__('subscription.forever_free')) ?></div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> <?= e(__('subscription.feature_weight')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> <?= e(__('subscription.feature_7day')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> <?= e(__('subscription.feature_calculators')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> <?= e(__('subscription.feature_food_basic')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> <?= e(__('subscription.feature_bilingual')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> <?= e(__('subscription.feature_encryption')) ?></li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent text-center py-3">
                        <a href="/register" class="btn btn-outline-primary w-100"><?= e(__('splash.start_free')) ?></a>
                    </div>
                </div>
            </div>

            <!-- Premium Tier -->
            <div class="col-md-4">
                <div class="card h-100 border-primary border-2 shadow">
                    <div class="card-header bg-primary text-white text-center py-3 position-relative">
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 mt-2 me-2"><?= e(__('subscription.most_popular')) ?></span>
                        <h5 class="fw-bold mb-1"><?= e(__('subscription.tier_premium')) ?></h5>
                        <div class="display-5 fw-bold">$7.99<small class="fs-6 fw-normal">/${__('subscription.month')}</small></div>
                        <div class="small opacity-75">$4.99/${__('subscription.month')} <?= e(__('splash.billed_yearly')) ?></div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> <?= e(__('subscription.everything_free')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> <?= e(__('subscription.feature_unlimited_food')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> <?= e(__('subscription.feature_full_analytics')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> <?= e(__('subscription.feature_planner')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> <?= e(__('subscription.feature_medications')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> <?= e(__('subscription.feature_appointments')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary"></i> <?= e(__('subscription.feature_export')) ?></li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent text-center py-3">
                        <a href="/register" class="btn btn-primary w-100 fw-semibold"><?= e(__('subscription.upgrade')) ?></a>
                    </div>
                </div>
            </div>

            <!-- Premium+ Tier -->
            <div class="col-md-4">
                <div class="card h-100 border shadow-sm">
                    <div class="card-header bg-dark text-white text-center py-3">
                        <h5 class="fw-bold mb-1"><?= e(__('subscription.tier_premium_plus')) ?></h5>
                        <div class="display-5 fw-bold">$14.99<small class="fs-6 fw-normal">/${__('subscription.month')}</small></div>
                        <div class="small opacity-75">$9.99/${__('subscription.month')} <?= e(__('splash.billed_yearly')) ?></div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-dark"></i> <?= e(__('subscription.everything_premium')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-dark"></i> <?= e(__('subscription.feature_cgm')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-dark"></i> <?= e(__('subscription.feature_provider_push')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-dark"></i> <?= e(__('subscription.feature_diabetes_analytics')) ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-dark"></i> <?= e(__('subscription.feature_caregiver')) ?></li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent text-center py-3">
                        <a href="/register" class="btn btn-dark w-100"><?= e(__('subscription.upgrade')) ?></a>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center text-muted small mt-4">
            <a href="/pricing" class="text-primary"><?= e(__('splash.full_pricing_details')) ?></a>
        </p>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-2"><?= e(__('splash.cta_final_title')) ?></h2>
        <p class="mb-4 opacity-75"><?= e(__('splash.cta_final_desc')) ?></p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="/register" class="btn btn-light btn-lg px-5 fw-semibold">
                <i class="bi bi-person-plus"></i> <?= e(__('splash.cta_register')) ?>
            </a>
            <a href="/login" class="btn btn-outline-light btn-lg px-5">
                <i class="bi bi-box-arrow-in-right"></i> <?= e(__('splash.cta_login')) ?>
            </a>
        </div>
    </div>
</section>
