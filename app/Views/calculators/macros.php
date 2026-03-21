<div class="row justify-content-center">
    <div class="col-md-8">
        <h4 class="mb-4"><i class="bi bi-pie-chart"></i> <?= e(__('calc.macro_title')) ?></h4>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="/calculator/macros">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="calories" class="form-label"><?= e(__('calc.recommended')) ?> (cal/day)</label>
                            <input type="number" class="form-control" id="calories" name="calories"
                                   value="<?= e($input['calories'] ?? '') ?>" required
                                   placeholder="e.g. 1800">
                            <div class="form-text">Use the Calorie Calculator to find this value.</div>
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
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted"><?= e(__('entry.protein')) ?></h6>
                        <div class="fs-3 fw-bold text-danger"><?= $result['protein_g'] ?>g</div>
                        <small class="text-muted"><?= $result['protein_cal'] ?> cal (<?= round($result['splits']['protein'] * 100) ?>%)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted"><?= e(__('entry.carbs')) ?></h6>
                        <div class="fs-3 fw-bold text-warning"><?= $result['carbs_g'] ?>g</div>
                        <small class="text-muted"><?= $result['carbs_cal'] ?> cal (<?= round($result['splits']['carbs'] * 100) ?>%)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted"><?= e(__('entry.fat')) ?></h6>
                        <div class="fs-3 fw-bold text-info"><?= $result['fat_g'] ?>g</div>
                        <small class="text-muted"><?= $result['fat_cal'] ?> cal (<?= round($result['splits']['fat'] * 100) ?>%)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="chart-container" style="height:250px;">
                            <canvas id="macroChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $scripts = '<script>
        new Chart(document.getElementById("macroChart"), {
            type: "doughnut",
            data: {
                labels: ["' . e(__('entry.protein')) . '", "' . e(__('entry.carbs')) . '", "' . e(__('entry.fat')) . '"],
                datasets: [{
                    data: [' . $result['protein_cal'] . ', ' . $result['carbs_cal'] . ', ' . $result['fat_cal'] . '],
                    backgroundColor: ["#dc3545", "#ffc107", "#0dcaf0"],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: "bottom" }
                }
            }
        });
        </script>'; ?>
        <?php endif; ?>
    </div>
</div>
