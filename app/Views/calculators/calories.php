<div class="row justify-content-center">
    <div class="col-md-8">
        <h4 class="mb-4"><i class="bi bi-calculator"></i> <?= e(__('calc.calorie_title')) ?></h4>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="/calculator/calories">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="age" class="form-label"><?= e(__('calc.age')) ?></label>
                            <input type="number" class="form-control" id="age" name="age"
                                   value="<?= e($input['age'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= e(__('calc.gender')) ?></label>
                            <select class="form-select" name="gender" required>
                                <option value="male" <?= ($input['gender'] ?? '') === 'male' ? 'selected' : '' ?>><?= e(__('calc.male')) ?></option>
                                <option value="female" <?= ($input['gender'] ?? '') === 'female' ? 'selected' : '' ?>><?= e(__('calc.female')) ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="weight" class="form-label"><?= e(__('entry.weight')) ?></label>
                            <input type="number" step="0.1" class="form-control" id="weight" name="weight"
                                   value="<?= e($input['weight'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="feet" class="form-label"><?= e(__('calc.feet')) ?></label>
                            <input type="number" class="form-control" id="feet" name="feet"
                                   value="<?= e($input['feet'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="inches" class="form-label"><?= e(__('calc.inches')) ?></label>
                            <input type="number" class="form-control" id="inches" name="inches"
                                   value="<?= e($input['inches'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= e(__('calc.activity_level')) ?></label>
                            <select class="form-select" name="activity_level" required>
                                <option value="sedentary" <?= ($input['activity_level'] ?? '') === 'sedentary' ? 'selected' : '' ?>><?= e(__('calc.sedentary')) ?></option>
                                <option value="light" <?= ($input['activity_level'] ?? '') === 'light' ? 'selected' : '' ?>><?= e(__('calc.light')) ?></option>
                                <option value="moderate" <?= ($input['activity_level'] ?? '') === 'moderate' ? 'selected' : '' ?>><?= e(__('calc.moderate')) ?></option>
                                <option value="active" <?= ($input['activity_level'] ?? '') === 'active' ? 'selected' : '' ?>><?= e(__('calc.active')) ?></option>
                                <option value="extra_active" <?= ($input['activity_level'] ?? '') === 'extra_active' ? 'selected' : '' ?>><?= e(__('calc.extra_active')) ?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= e(__('calc.goal')) ?></label>
                            <select class="form-select" name="goal" required>
                                <option value="lose" <?= ($input['goal'] ?? '') === 'lose' ? 'selected' : '' ?>><?= e(__('calc.lose')) ?></option>
                                <option value="maintain" <?= ($input['goal'] ?? '') === 'maintain' ? 'selected' : '' ?>><?= e(__('calc.maintain')) ?></option>
                                <option value="gain" <?= ($input['goal'] ?? '') === 'gain' ? 'selected' : '' ?>><?= e(__('calc.gain')) ?></option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-calculator"></i> <?= e(__('calc.calculate')) ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($result): ?>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <h6 class="text-muted"><?= e(__('calc.your_bmr')) ?></h6>
                        <div class="fs-3 fw-bold text-info"><?= $result['bmr'] ?></div>
                        <small class="text-muted">cal/day</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <h6 class="text-muted"><?= e(__('calc.your_tdee')) ?></h6>
                        <div class="fs-3 fw-bold text-warning"><?= $result['tdee'] ?></div>
                        <small class="text-muted">cal/day</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <h6 class="text-muted"><?= e(__('calc.recommended')) ?></h6>
                        <div class="fs-3 fw-bold text-success"><?= $result['recommended'] ?></div>
                        <small class="text-muted">cal/day</small>
                    </div>
                </div>
            </div>
            <?php if ($result['deficit'] > 0): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <?= e(__('calc.deficit')) ?>: <strong><?= $result['deficit'] ?> calories/day</strong>
                    (~<?= round($result['deficit'] * 7 / 3500, 1) ?> lbs/week)
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
