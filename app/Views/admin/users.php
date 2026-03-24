<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-people"></i> Manage Users</h4>
    <a href="/admin" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Admin</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Lang</th>
                    <th>Registered</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= e($u['first_name']) ?> <?= e($u['last_name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td>
                        <span class="badge bg-<?= ($u['role'] ?? 'user') === 'admin' ? 'danger' : 'secondary' ?>">
                            <?= e($u['role'] ?? 'user') ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($u['email_verified']): ?>
                            <i class="bi bi-check-circle text-success"></i>
                        <?php else: ?>
                            <i class="bi bi-x-circle text-muted"></i>
                        <?php endif; ?>
                    </td>
                    <td><?= e($u['language']) ?></td>
                    <td><?= e(formatDate($u['created_at'], 'M j, Y')) ?></td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/admin/users/<?= $u['id'] ?>"><i class="bi bi-pencil"></i> Edit</a></li>
                                <li><a class="dropdown-item" href="/admin/users/<?= $u['id'] ?>/export"><i class="bi bi-download"></i> Export Data</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="/admin/users/<?= $u['id'] ?>/delete" class="px-3">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this user and ALL their data?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
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
