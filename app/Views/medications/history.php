<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><i class="bi bi-clock-history"></i> <?= e(__('medications.history')) ?></h4>
                <p class="text-muted mb-0"><?= e($medication['name']) ?> <?= $medication['dosage'] ? '- ' . e($medication['dosage']) : '' ?></p>
            </div>
            <a href="/medications" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> <?= e(__('back')) ?>
            </a>
        </div>

        <?php if (empty($history)): ?>
            <div class="card">
                <div class="card-body text-center py-4 text-muted">
                    <?= e(__('medications.no_history')) ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($history as $event): ?>
                        <div class="d-flex mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <?php
                                $iconMap = [
                                    'added' => 'bi-plus-circle text-success',
                                    'updated' => 'bi-pencil text-primary',
                                    'discontinued' => 'bi-x-circle text-danger',
                                    'reactivated' => 'bi-arrow-counterclockwise text-success',
                                ];
                                $icon = $iconMap[$event['action']] ?? 'bi-circle text-muted';
                                ?>
                                <i class="bi <?= $icon ?>" style="font-size:1.25rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold"><?= e(ucfirst($event['action'])) ?></div>
                                <?php if ($event['details']): ?>
                                    <div class="text-muted small"><?= e($event['details']) ?></div>
                                <?php endif; ?>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-person"></i> <?= e(ucfirst($event['changed_by'])) ?>
                                    &middot; <?= e(formatDate($event['created_at'], 'M j, Y g:i A')) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
