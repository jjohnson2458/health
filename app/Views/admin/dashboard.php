<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Admin Dashboard</h4>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-start border-primary border-3">
            <div class="card-body py-3">
                <div class="text-muted small">Total Users</div>
                <div class="fs-4 fw-bold"><?= $totalUsers ?></div>
                <a href="/admin/users" class="small">Manage &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-start border-success border-3">
            <div class="card-body py-3">
                <div class="text-muted small">Health Entries</div>
                <div class="fs-4 fw-bold"><?= $totalEntries ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-start border-info border-3">
            <div class="card-body py-3">
                <div class="text-muted small">Medications</div>
                <div class="fs-4 fw-bold"><?= $totalMeds ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-start border-warning border-3">
            <div class="card-body py-3">
                <div class="text-muted small">Appointments</div>
                <div class="fs-4 fw-bold"><?= $totalAppts ?></div>
            </div>
        </div>
    </div>
</div>

<?php if ($recentErrors > 0): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i> <strong><?= $recentErrors ?></strong> error(s) in the last 24 hours.
    <a href="/admin/errors" class="alert-link">View error log &rarr;</a>
</div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Quick Actions</div>
            <div class="card-body">
                <a href="/admin/users" class="btn btn-outline-primary btn-sm me-1 mb-1"><i class="bi bi-people"></i> Manage Users</a>
                <a href="/admin/errors" class="btn btn-outline-danger btn-sm me-1 mb-1"><i class="bi bi-bug"></i> Error Log</a>
            </div>
        </div>
    </div>
</div>
