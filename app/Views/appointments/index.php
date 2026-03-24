<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-calendar-event"></i> <?= e(__('appointments.title')) ?></h4>
    <div>
        <a href="/appointments/calendar" class="btn btn-outline-secondary btn-sm me-1">
            <i class="bi bi-calendar3"></i> <?= e(__('appointments.calendar')) ?>
        </a>
        <a href="/appointments/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> <?= e(__('appointments.add')) ?>
        </a>
    </div>
</div>

<!-- Upcoming/Past Tabs -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#upcoming" role="tab">
            <i class="bi bi-calendar-check text-primary"></i> <?= e(__('appointments.upcoming')) ?>
            <span class="badge bg-primary"><?= count($upcoming) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#past" role="tab">
            <i class="bi bi-calendar-x text-muted"></i> <?= e(__('appointments.past')) ?>
            <span class="badge bg-secondary"><?= count($past) ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Upcoming -->
    <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
        <?php if (empty($upcoming)): ?>
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-calendar-event text-muted" style="font-size:2rem;"></i>
                    <p class="text-muted mt-2 mb-2"><?= e(__('appointments.no_upcoming')) ?></p>
                    <a href="/appointments/create" class="btn btn-primary btn-sm"><?= e(__('appointments.add')) ?></a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($upcoming as $appt): ?>
                <div class="col-md-6">
                    <div class="card border-start border-primary border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="bi bi-calendar"></i>
                                        <?= e(formatDate($appt['appointment_date'], 'D, M j, Y')) ?>
                                        <?php if ($appt['appointment_time']): ?>
                                            <span class="text-muted">@ <?= e(date('g:i A', strtotime($appt['appointment_time']))) ?></span>
                                        <?php endif; ?>
                                    </h6>
                                    <span class="badge bg-light text-dark"><?= e(ucfirst($appt['type'])) ?></span>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="/appointments/<?= $appt['id'] ?>"><i class="bi bi-pencil"></i> <?= e(__('edit')) ?></a></li>
                                        <li>
                                            <form method="POST" action="/appointments/<?= $appt['id'] ?>/complete" class="px-3">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle"></i> <?= e(__('appointments.mark_complete')) ?></button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="/appointments/<?= $appt['id'] ?>/cancel" class="px-3">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('<?= e(__('appointments.confirm_cancel')) ?>')"><i class="bi bi-x-circle"></i> <?= e(__('appointments.cancel_appt')) ?></button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php if ($appt['provider_name']): ?>
                                <div class="text-muted small mt-1"><i class="bi bi-person"></i> <?= e($appt['provider_name']) ?></div>
                            <?php endif; ?>
                            <?php if ($appt['location']): ?>
                                <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= e($appt['location']) ?></div>
                            <?php endif; ?>
                            <?php if ($appt['notes']): ?>
                                <div class="small text-muted mt-1"><i class="bi bi-sticky"></i> <?= e($appt['notes']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Past -->
    <div class="tab-pane fade" id="past" role="tabpanel">
        <?php if (empty($past)): ?>
            <div class="card">
                <div class="card-body text-center py-4 text-muted">
                    <?= e(__('appointments.no_past')) ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($past as $appt): ?>
                <div class="col-md-6">
                    <div class="card border-start border-secondary border-3 opacity-75">
                        <div class="card-body">
                            <h6 class="mb-1">
                                <?= e(formatDate($appt['appointment_date'], 'D, M j, Y')) ?>
                                <span class="badge bg-<?= $appt['status'] === 'completed' ? 'success' : 'danger' ?> ms-1"><?= e(ucfirst($appt['status'])) ?></span>
                            </h6>
                            <span class="badge bg-light text-dark"><?= e(ucfirst($appt['type'])) ?></span>
                            <?php if ($appt['provider_name']): ?>
                                <div class="text-muted small mt-1"><i class="bi bi-person"></i> <?= e($appt['provider_name']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
