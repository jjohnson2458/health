<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <h4 class="mb-0"><i class="bi bi-printer"></i> <?= e(__('medications.share')) ?></h4>
            <div>
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer"></i> <?= e(__('medications.print')) ?>
                </button>
                <a href="/medications" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> <?= e(__('back')) ?>
                </a>
            </div>
        </div>

        <div class="card shadow-sm" id="printable-list">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h4><?= e(__('medications.medication_list')) ?></h4>
                    <p class="text-muted">
                        <?= e($user['first_name'] ?? '') ?> <?= e($user['last_name'] ?? '') ?>
                        &middot; <?= e(__('medications.generated_on')) ?> <?= date('F j, Y') ?>
                    </p>
                </div>

                <?php if (empty($medications)): ?>
                    <p class="text-center text-muted"><?= e(__('medications.no_active')) ?></p>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th><?= e(__('medications.name')) ?></th>
                                <th><?= e(__('medications.dosage')) ?></th>
                                <th><?= e(__('medications.frequency')) ?></th>
                                <th><?= e(__('medications.prescriber')) ?></th>
                                <th><?= e(__('medications.prescribed_date')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medications as $med): ?>
                            <tr>
                                <td><strong><?= e($med['name']) ?></strong></td>
                                <td><?= e($med['dosage'] ?? '--') ?></td>
                                <td><?= e($med['frequency'] ? ucfirst($med['frequency']) : '--') ?></td>
                                <td><?= e($med['prescriber_name'] ?? '--') ?></td>
                                <td><?= $med['prescribed_date'] ? e(formatDate($med['prescribed_date'])) : '--' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="text-muted small text-center mt-3">
                        <?= e(__('medications.active_count', ['count' => count($medications)])) ?>
                    </p>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <span class="badge bg-success">
                        <i class="bi bi-shield-lock"></i> <?= e(__('hipaa_notice')) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .d-print-none { display: none !important; }
    nav, footer, .navbar { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
