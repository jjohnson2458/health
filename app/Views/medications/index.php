<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-capsule"></i> <?= e(__('medications.title')) ?></h4>
    <div>
        <a href="/medications/share" class="btn btn-outline-secondary btn-sm me-1">
            <i class="bi bi-printer"></i> <?= e(__('medications.share')) ?>
        </a>
        <a href="/medications/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> <?= e(__('medications.add')) ?>
        </a>
    </div>
</div>

<!-- Active/Discontinued Tabs -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#active" role="tab">
            <i class="bi bi-check-circle text-success"></i> <?= e(__('medications.active')) ?>
            <span class="badge bg-success"><?= count($active) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#discontinued" role="tab">
            <i class="bi bi-x-circle text-muted"></i> <?= e(__('medications.discontinued_tab')) ?>
            <span class="badge bg-secondary"><?= count($discontinued) ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Active Medications -->
    <div class="tab-pane fade show active" id="active" role="tabpanel">
        <?php if (empty($active)): ?>
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-capsule text-muted" style="font-size:2rem;"></i>
                    <p class="text-muted mt-2 mb-2"><?= e(__('medications.no_active')) ?></p>
                    <a href="/medications/create" class="btn btn-primary btn-sm"><?= e(__('medications.add')) ?></a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($active as $med): ?>
                <div class="col-md-6">
                    <div class="card border-start border-success border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= e($med['name']) ?></h6>
                                    <?php if ($med['dosage']): ?>
                                        <span class="badge bg-light text-dark"><?= e($med['dosage']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($med['frequency']): ?>
                                        <span class="badge bg-light text-dark"><?= e($med['frequency']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="/medications/<?= $med['id'] ?>"><i class="bi bi-pencil"></i> <?= e(__('edit')) ?></a></li>
                                        <li><a class="dropdown-item" href="/medications/<?= $med['id'] ?>/history"><i class="bi bi-clock-history"></i> <?= e(__('medications.history')) ?></a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="/medications/<?= $med['id'] ?>/discontinue" class="px-3">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('<?= e(__('medications.confirm_discontinue')) ?>')">
                                                    <i class="bi bi-x-circle"></i> <?= e(__('medications.discontinue')) ?>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php if ($med['prescriber_name']): ?>
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-person"></i> <?= e($med['prescriber_name']) ?>
                                    <?php if ($med['prescribed_date']): ?>
                                        &middot; <?= e(formatDate($med['prescribed_date'])) ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($med['notes']): ?>
                                <div class="small text-muted mt-1"><i class="bi bi-sticky"></i> <?= e($med['notes']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Discontinued Medications -->
    <div class="tab-pane fade" id="discontinued" role="tabpanel">
        <?php if (empty($discontinued)): ?>
            <div class="card">
                <div class="card-body text-center py-4 text-muted">
                    <?= e(__('medications.no_discontinued')) ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($discontinued as $med): ?>
                <div class="col-md-6">
                    <div class="card border-start border-secondary border-3 opacity-75">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 text-decoration-line-through"><?= e($med['name']) ?></h6>
                                    <?php if ($med['dosage']): ?>
                                        <span class="badge bg-light text-dark"><?= e($med['dosage']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="/medications/<?= $med['id'] ?>/history"><i class="bi bi-clock-history"></i> <?= e(__('medications.history')) ?></a></li>
                                        <li>
                                            <form method="POST" action="/medications/<?= $med['id'] ?>/reactivate" class="px-3">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item text-success">
                                                    <i class="bi bi-arrow-counterclockwise"></i> <?= e(__('medications.reactivate')) ?>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php if ($med['discontinued_date']): ?>
                                <div class="text-muted small mt-1">
                                    <?= e(__('medications.discontinued_on')) ?> <?= e(formatDate($med['discontinued_date'])) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($med['discontinued_reason']): ?>
                                <div class="small text-muted mt-1"><i class="bi bi-info-circle"></i> <?= e($med['discontinued_reason']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
