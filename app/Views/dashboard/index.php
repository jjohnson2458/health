<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><?= e(__('dashboard.title')) ?></h4>
    <a href="/entry" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> <?= e(__('dashboard.add_entry')) ?>
    </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card weight">
            <div class="card-body py-3">
                <div class="text-muted small"><?= e(__('dashboard.current_weight')) ?></div>
                <div class="fs-4 fw-bold"><?= $currentWeight ? e($currentWeight) . ' lbs' : '--' ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card calories">
            <div class="card-body py-3">
                <div class="text-muted small"><?= e(__('dashboard.avg_calories')) ?></div>
                <div class="fs-4 fw-bold"><?= $avgCalories ?: '--' ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card streak">
            <div class="card-body py-3">
                <div class="text-muted small"><?= e(__('dashboard.streak')) ?></div>
                <div class="fs-4 fw-bold"><?= $streak ?> <?= e(__('dashboard.days')) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card heart">
            <div class="card-body py-3">
                <div class="text-muted small"><?= e(__('dashboard.today')) ?></div>
                <div class="fs-4 fw-bold">
                    <?php if ($todayEntry): ?>
                        <i class="bi bi-check-circle text-success"></i>
                    <?php else: ?>
                        <i class="bi bi-dash-circle text-muted"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Entry -->
<?php if ($todayEntry): ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-check"></i> <?= e(__('dashboard.today')) ?></span>
        <a href="/entry/<?= $todayEntry['id'] ?>" class="btn btn-sm btn-outline-primary"><?= e(__('edit')) ?></a>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-4 col-md-2">
                <div class="text-muted small"><?= e(__('entry.weight')) ?></div>
                <strong><?= $todayEntry['weight'] ? e($todayEntry['weight']) : '--' ?></strong>
            </div>
            <div class="col-4 col-md-2">
                <div class="text-muted small"><?= e(__('entry.calories')) ?></div>
                <strong><?= $todayEntry['calories'] ?? '--' ?></strong>
            </div>
            <div class="col-4 col-md-2">
                <div class="text-muted small"><?= e(__('entry.heart_rate')) ?></div>
                <strong><?= $todayEntry['heart_rate'] ?? '--' ?></strong>
            </div>
            <div class="col-4 col-md-2">
                <div class="text-muted small"><?= e(__('entry.blood_sugar')) ?></div>
                <strong><?= $todayEntry['blood_sugar'] ?? '--' ?></strong>
            </div>
            <div class="col-4 col-md-2">
                <div class="text-muted small"><?= e(__('entry.protein')) ?></div>
                <strong><?= $todayEntry['protein_g'] ?? '--' ?>g</strong>
            </div>
            <div class="col-4 col-md-2">
                <div class="text-muted small"><?= e(__('entry.exercise_minutes')) ?></div>
                <strong><?= $todayEntry['exercise_minutes'] ?? '--' ?> min</strong>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card mb-4 border-dashed">
    <div class="card-body text-center py-4">
        <p class="text-muted mb-2"><?= e(__('dashboard.no_entry')) ?></p>
        <a href="/entry" class="btn btn-primary btn-sm"><?= e(__('dashboard.add_entry')) ?></a>
    </div>
</div>
<?php endif; ?>

<!-- 7-Day Charts -->
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= e(__('dashboard.weekly_trend')) ?> - <?= e(__('entry.weight')) ?></div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="weightChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= e(__('dashboard.weekly_trend')) ?> - <?= e(__('entry.calories')) ?></div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="caloriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $scripts = '<script>
const labels = ' . $chartLabels . ';
const weightData = ' . $chartWeight . ';
const caloriesData = ' . $chartCalories . ';

if (labels.length > 0) {
    new Chart(document.getElementById("weightChart"), {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "' . e(__('entry.weight')) . '",
                data: weightData,
                borderColor: "#0d6efd",
                backgroundColor: "rgba(13, 110, 253, 0.1)",
                fill: true,
                tension: 0.3,
                spanGaps: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    new Chart(document.getElementById("caloriesChart"), {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "' . e(__('entry.calories')) . '",
                data: caloriesData,
                backgroundColor: "rgba(255, 193, 7, 0.6)",
                borderColor: "#ffc107",
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
}
</script>'; ?>
