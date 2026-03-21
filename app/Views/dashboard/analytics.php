<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-graph-up"></i> <?= e(__('analytics.title')) ?></h4>
    <div class="btn-group btn-group-sm" id="rangeSelector">
        <button class="btn btn-outline-primary" data-range="week"><?= e(__('analytics.week')) ?></button>
        <button class="btn btn-outline-primary active" data-range="month"><?= e(__('analytics.month')) ?></button>
        <button class="btn btn-outline-primary" data-range="3months"><?= e(__('analytics.3months')) ?></button>
        <button class="btn btn-outline-primary" data-range="6months"><?= e(__('analytics.6months')) ?></button>
        <button class="btn btn-outline-primary" data-range="year"><?= e(__('analytics.year')) ?></button>
    </div>
</div>

<!-- Main Charts -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= e(__('analytics.weight_over_time')) ?></div>
            <div class="card-body"><div class="chart-container"><canvas id="weightChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= e(__('analytics.calories_over_time')) ?></div>
            <div class="card-body"><div class="chart-container"><canvas id="caloriesChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= e(__('analytics.heart_rate_over_time')) ?></div>
            <div class="card-body"><div class="chart-container"><canvas id="heartRateChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= e(__('analytics.blood_sugar_over_time')) ?></div>
            <div class="card-body"><div class="chart-container"><canvas id="bloodSugarChart"></canvas></div></div>
        </div>
    </div>
</div>

<!-- Day of Week -->
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= e(__('analytics.by_day_of_week')) ?> - <?= e(__('entry.calories')) ?></div>
            <div class="card-body"><div class="chart-container"><canvas id="dowCaloriesChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= e(__('analytics.exercise_summary')) ?></div>
            <div class="card-body"><div class="chart-container"><canvas id="dowExerciseChart"></canvas></div></div>
        </div>
    </div>
</div>

<?php $scripts = '<script>
let charts = {};

function createLineChart(id, label, data, color) {
    if (charts[id]) charts[id].destroy();
    charts[id] = new Chart(document.getElementById(id), {
        type: "line",
        data: {
            labels: data.labels,
            datasets: [{
                label: label,
                data: data.values,
                borderColor: color,
                backgroundColor: color + "20",
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
}

function createBarChart(id, labels, data, color, label) {
    if (charts[id]) charts[id].destroy();
    charts[id] = new Chart(document.getElementById(id), {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: color + "99",
                borderColor: color,
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

function loadData(range) {
    fetch("/analytics/data?range=" + range)
        .then(r => r.json())
        .then(d => {
            createLineChart("weightChart", "Weight", {labels: d.labels, values: d.weight}, "#0d6efd");
            createLineChart("caloriesChart", "Calories", {labels: d.labels, values: d.calories}, "#ffc107");
            createLineChart("heartRateChart", "Heart Rate", {labels: d.labels, values: d.heart_rate}, "#dc3545");
            createLineChart("bloodSugarChart", "Blood Sugar", {labels: d.labels, values: d.blood_sugar}, "#6f42c1");

            const days = Object.keys(d.dayOfWeek);
            const dowCal = days.map(d2 => d.dayOfWeek[d2].calories);
            const dowEx = days.map(d2 => d.dayOfWeek[d2].exercise_minutes);

            createBarChart("dowCaloriesChart", days, dowCal, "#ffc107", "Avg Calories");
            createBarChart("dowExerciseChart", days, dowEx, "#198754", "Avg Exercise (min)");
        });
}

document.querySelectorAll("#rangeSelector button").forEach(btn => {
    btn.addEventListener("click", function() {
        document.querySelector("#rangeSelector .active").classList.remove("active");
        this.classList.add("active");
        loadData(this.dataset.range);
    });
});

loadData("month");
</script>'; ?>
