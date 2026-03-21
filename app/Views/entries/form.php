<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-plus"></i> <?= e(__('entry.title')) ?>
            </div>
            <div class="card-body entry-form">
                <form method="POST" action="<?= $entry ? '/entry/' . $entry['id'] : '/entry' ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <!-- Date -->
                        <div class="col-md-4">
                            <label for="entry_date" class="form-label"><?= e(__('entry.date')) ?></label>
                            <input type="date" class="form-control" id="entry_date" name="entry_date"
                                   value="<?= e($entry['entry_date'] ?? $date) ?>"
                                   max="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- Weight -->
                        <div class="col-md-4">
                            <label for="weight" class="form-label"><?= e(__('entry.weight')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="weight" name="weight"
                                   value="<?= e($entry['weight'] ?? '') ?>" placeholder="e.g. 185.5">
                        </div>

                        <!-- Calories -->
                        <div class="col-md-4">
                            <label for="calories" class="form-label"><?= e(__('entry.calories')) ?></label>
                            <input type="number" class="form-control" id="calories" name="calories"
                                   value="<?= e($entry['calories'] ?? '') ?>" placeholder="e.g. 2000">
                        </div>

                        <!-- Macros -->
                        <div class="col-md-4">
                            <label for="protein_g" class="form-label"><?= e(__('entry.protein')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="protein_g" name="protein_g"
                                   value="<?= e($entry['protein_g'] ?? '') ?>" placeholder="e.g. 120">
                        </div>
                        <div class="col-md-4">
                            <label for="carbs_g" class="form-label"><?= e(__('entry.carbs')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="carbs_g" name="carbs_g"
                                   value="<?= e($entry['carbs_g'] ?? '') ?>" placeholder="e.g. 200">
                        </div>
                        <div class="col-md-4">
                            <label for="fat_g" class="form-label"><?= e(__('entry.fat')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="fat_g" name="fat_g"
                                   value="<?= e($entry['fat_g'] ?? '') ?>" placeholder="e.g. 65">
                        </div>

                        <!-- Vitals -->
                        <div class="col-md-6">
                            <label for="heart_rate" class="form-label"><?= e(__('entry.heart_rate')) ?></label>
                            <input type="number" class="form-control" id="heart_rate" name="heart_rate"
                                   value="<?= e($entry['heart_rate'] ?? '') ?>" placeholder="e.g. 72">
                        </div>
                        <div class="col-md-6">
                            <label for="blood_sugar" class="form-label"><?= e(__('entry.blood_sugar')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="blood_sugar" name="blood_sugar"
                                   value="<?= e($entry['blood_sugar'] ?? '') ?>" placeholder="e.g. 95.0">
                        </div>

                        <!-- Exercise -->
                        <div class="col-md-6">
                            <label for="exercise_type" class="form-label"><?= e(__('entry.exercise_type')) ?></label>
                            <select class="form-select" id="exercise_type" name="exercise_type">
                                <option value="">-- Select --</option>
                                <?php
                                $types = ['Walking', 'Running', 'Cycling', 'Swimming', 'Weight Training', 'Yoga', 'HIIT', 'Other'];
                                foreach ($types as $type):
                                ?>
                                <option value="<?= e($type) ?>" <?= ($entry['exercise_type'] ?? '') === $type ? 'selected' : '' ?>>
                                    <?= e($type) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="exercise_minutes" class="form-label"><?= e(__('entry.exercise_minutes')) ?></label>
                            <input type="number" class="form-control" id="exercise_minutes" name="exercise_minutes"
                                   value="<?= e($entry['exercise_minutes'] ?? '') ?>" placeholder="e.g. 30">
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label"><?= e(__('entry.notes')) ?></label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Optional notes..."><?= e($entry['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> <?= e(__('save')) ?>
                        </button>
                        <a href="/dashboard" class="btn btn-secondary"><?= e(__('cancel')) ?></a>
                        <?php if ($entry): ?>
                        <form method="POST" action="/entry/<?= $entry['id'] ?>/delete" class="ms-auto"
                              onsubmit="return confirm('Are you sure?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> <?= e(__('delete')) ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
