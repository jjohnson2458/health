<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="bi bi-capsule"></i>
                <?= $medication ? e(__('medications.edit')) : e(__('medications.add')) ?>
            </h4>
            <a href="/medications" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> <?= e(__('back')) ?>
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?= $medication ? '/medications/' . $medication['id'] : '/medications' ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label"><?= e(__('medications.name')) ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= e($medication['name'] ?? $old['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="dosage" class="form-label"><?= e(__('medications.dosage')) ?></label>
                            <input type="text" class="form-control" id="dosage" name="dosage"
                                   placeholder="e.g., 10mg, 500mg"
                                   value="<?= e($medication['dosage'] ?? $old['dosage'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="frequency" class="form-label"><?= e(__('medications.frequency')) ?></label>
                            <select class="form-select" id="frequency" name="frequency">
                                <option value="">-- <?= e(__('medications.select_frequency')) ?> --</option>
                                <?php
                                $frequencies = ['once daily', 'twice daily', 'three times daily', 'four times daily', 'weekly', 'biweekly', 'monthly', 'as needed'];
                                $currentFreq = $medication['frequency'] ?? $old['frequency'] ?? '';
                                foreach ($frequencies as $freq): ?>
                                    <option value="<?= e($freq) ?>" <?= $currentFreq === $freq ? 'selected' : '' ?>><?= e(ucfirst($freq)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="prescribed_date" class="form-label"><?= e(__('medications.prescribed_date')) ?></label>
                            <input type="date" class="form-control" id="prescribed_date" name="prescribed_date"
                                   value="<?= e($medication['prescribed_date'] ?? $old['prescribed_date'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-8">
                            <label for="prescriber_name" class="form-label"><?= e(__('medications.prescriber')) ?></label>
                            <input type="text" class="form-control" id="prescriber_name" name="prescriber_name"
                                   placeholder="<?= e(__('medications.prescriber_placeholder')) ?>"
                                   value="<?= e($medication['prescriber_name'] ?? $old['prescriber_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="prescriber_npi" class="form-label"><?= e(__('medications.npi')) ?></label>
                            <input type="text" class="form-control" id="prescriber_npi" name="prescriber_npi"
                                   placeholder="10-digit NPI" maxlength="10"
                                   value="<?= e($medication['prescriber_npi'] ?? $old['prescriber_npi'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="notes" class="form-label"><?= e(__('medications.notes')) ?></label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="<?= e(__('medications.notes_placeholder')) ?>"><?= e($medication['notes'] ?? $old['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> <?= e(__('save')) ?>
                        </button>
                        <a href="/medications" class="btn btn-outline-secondary"><?= e(__('cancel')) ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
