<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="bi bi-calendar-event"></i>
                <?= $appointment ? e(__('appointments.edit')) : e(__('appointments.add')) ?>
            </h4>
            <a href="/appointments" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> <?= e(__('back')) ?>
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?= $appointment ? '/appointments/' . $appointment['id'] : '/appointments' ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="appointment_date" class="form-label"><?= e(__('appointments.date')) ?> <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                                   value="<?= e($appointment['appointment_date'] ?? $old['appointment_date'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="appointment_time" class="form-label"><?= e(__('appointments.time')) ?></label>
                            <input type="time" class="form-control" id="appointment_time" name="appointment_time"
                                   value="<?= e($appointment['appointment_time'] ?? $old['appointment_time'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="provider_name" class="form-label"><?= e(__('appointments.provider')) ?></label>
                            <input type="text" class="form-control" id="provider_name" name="provider_name"
                                   placeholder="<?= e(__('appointments.provider_placeholder')) ?>"
                                   value="<?= e($appointment['provider_name'] ?? $old['provider_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label"><?= e(__('appointments.type')) ?></label>
                            <select class="form-select" id="type" name="type">
                                <?php
                                $types = ['checkup', 'lab', 'specialist', 'dental', 'vision', 'therapy', 'other'];
                                $currentType = $appointment['type'] ?? $old['type'] ?? 'checkup';
                                foreach ($types as $t): ?>
                                    <option value="<?= e($t) ?>" <?= $currentType === $t ? 'selected' : '' ?>><?= e(ucfirst($t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="location" class="form-label"><?= e(__('appointments.location')) ?></label>
                        <input type="text" class="form-control" id="location" name="location"
                               placeholder="<?= e(__('appointments.location_placeholder')) ?>"
                               value="<?= e($appointment['location'] ?? $old['location'] ?? '') ?>">
                    </div>

                    <div class="mt-3">
                        <label for="notes" class="form-label"><?= e(__('appointments.notes')) ?></label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="<?= e(__('appointments.notes_placeholder')) ?>"><?= e($appointment['notes'] ?? $old['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> <?= e(__('save')) ?>
                        </button>
                        <a href="/appointments" class="btn btn-outline-secondary"><?= e(__('cancel')) ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
