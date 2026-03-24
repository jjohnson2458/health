<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-bug"></i> Error Log</h4>
    <a href="/admin" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Admin</a>
</div>

<?php if (empty($errors)): ?>
    <div class="card">
        <div class="card-body text-center py-4 text-muted">No errors recorded.</div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0" style="font-size:12px;">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>Level</th>
                        <th>Message</th>
                        <th>File</th>
                        <th>User</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($errors as $err): ?>
                    <tr>
                        <td class="text-nowrap"><?= e(formatDate($err['created_at'], 'M j g:i A')) ?></td>
                        <td>
                            <span class="badge bg-<?= $err['error_level'] === 'EXCEPTION' || $err['error_level'] === 'ERROR' ? 'danger' : 'warning' ?>">
                                <?= e($err['error_level']) ?>
                            </span>
                        </td>
                        <td style="max-width:300px;" class="text-truncate" title="<?= e($err['message']) ?>"><?= e($err['message']) ?></td>
                        <td class="text-truncate" style="max-width:150px;" title="<?= e($err['file']) ?>:<?= $err['line'] ?>">
                            <?= $err['file'] ? e(basename($err['file'])) . ':' . $err['line'] : '--' ?>
                        </td>
                        <td><?= $err['user_id'] ?? '--' ?></td>
                        <td><?= e($err['ip_address']) ?></td>
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
