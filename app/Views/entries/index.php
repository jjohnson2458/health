<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-journal-text"></i> <?= e(__('entry.history_title')) ?></h4>
    <div>
        <a href="/export/csv" class="btn btn-outline-success btn-sm me-1">
            <i class="bi bi-download"></i> <?= e(__('export.csv')) ?>
        </a>
        <a href="/entry" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> <?= e(__('dashboard.add_entry')) ?>
        </a>
    </div>
</div>

<?php if (empty($entries)): ?>
    <div class="card">
        <div class="card-body text-center py-4">
            <p class="text-muted mb-2"><?= e(__('entry.no_entries')) ?></p>
            <a href="/entry" class="btn btn-primary btn-sm"><?= e(__('dashboard.add_entry')) ?></a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th><?= e(__('entry.date')) ?></th>
                        <th><?= e(__('entry.weight')) ?></th>
                        <th><?= e(__('entry.calories')) ?></th>
                        <th class="d-none d-md-table-cell"><?= e(__('entry.heart_rate')) ?></th>
                        <th class="d-none d-md-table-cell"><?= e(__('entry.blood_sugar')) ?></th>
                        <th class="d-none d-lg-table-cell"><?= e(__('entry.exercise_minutes')) ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?= e(formatDate($entry['entry_date'])) ?></td>
                        <td><?= $entry['weight'] ? e($entry['weight']) : '--' ?></td>
                        <td><?= $entry['calories'] ?? '--' ?></td>
                        <td class="d-none d-md-table-cell"><?= $entry['heart_rate'] ?? '--' ?></td>
                        <td class="d-none d-md-table-cell"><?= $entry['blood_sugar'] ?? '--' ?></td>
                        <td class="d-none d-lg-table-cell"><?= $entry['exercise_minutes'] ?? '--' ?> min</td>
                        <td class="text-end">
                            <a href="/entry/<?= $entry['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <?php include __DIR__ . '/../partials/pagination.php'; ?>
    </div>
<?php endif; ?>
