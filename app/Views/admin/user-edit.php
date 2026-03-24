<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="bi bi-person-gear"></i> Edit User #<?= $editUser['id'] ?></h4>
            <a href="/admin/users" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header">User Info (read-only)</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><strong>Name:</strong> <?= e($editUser['first_name']) ?> <?= e($editUser['last_name']) ?></div>
                    <div class="col-md-6"><strong>Email:</strong> <?= e($editUser['email']) ?></div>
                    <div class="col-md-6 mt-2"><strong>Registered:</strong> <?= e(formatDate($editUser['created_at'], 'M j, Y g:i A')) ?></div>
                    <div class="col-md-6 mt-2"><strong>Last Updated:</strong> <?= e(formatDate($editUser['updated_at'], 'M j, Y g:i A')) ?></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Edit Settings</div>
            <div class="card-body p-4">
                <form method="POST" action="/admin/users/<?= $editUser['id'] ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role">
                                <option value="user" <?= ($editUser['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= ($editUser['role'] ?? 'user') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="email_verified" class="form-label">Email Verified</label>
                            <select class="form-select" id="email_verified" name="email_verified">
                                <option value="1" <?= $editUser['email_verified'] ? 'selected' : '' ?>>Yes</option>
                                <option value="0" <?= !$editUser['email_verified'] ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="language" class="form-label">Language</label>
                            <select class="form-select" id="language" name="language">
                                <option value="en" <?= $editUser['language'] === 'en' ? 'selected' : '' ?>>English</option>
                                <option value="es" <?= $editUser['language'] === 'es' ? 'selected' : '' ?>>Spanish</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="new_password" class="form-label">Reset Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="8"
                               placeholder="New password (min 8 characters)">
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Changes</button>
                        <a href="/admin/users/<?= $editUser['id'] ?>/export" class="btn btn-outline-success"><i class="bi bi-download"></i> Export Patient Data</a>
                        <a href="/admin/users" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
