<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="bi bi-calendar-check"></i> <?= e(__('planner.title')) ?></h4>
            <a href="/planner" class="btn btn-outline-secondary btn-sm"><?= e(__('back')) ?></a>
        </div>

        <!-- Summary -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-muted small">Start</div>
                        <div class="fs-4 fw-bold"><?= $currentWeight ?> lbs</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-muted small">Goal</div>
                        <div class="fs-4 fw-bold text-success"><?= $goalWeight ?> lbs</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-muted small">Rate</div>
                        <div class="fs-4 fw-bold"><?= $weeklyGoal ?> lbs/wk</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-muted small">Timeline</div>
                        <div class="fs-4 fw-bold"><?= $weeksNeeded ?> weeks</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart: Projected vs Actual -->
        <div class="card mb-4">
            <div class="card-header"><?= e(__('planner.projected')) ?> vs <?= e(__('planner.actual')) ?></div>
            <div class="card-body">
                <div class="chart-container" style="height:350px;">
                    <canvas id="plannerChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <?php if (!empty($recommendations)): ?>
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-lightbulb"></i> Recommendations</div>
            <div class="card-body">
                <?php foreach ($recommendations as $rec): ?>
                <div class="d-flex mb-3">
                    <div class="me-3">
                        <?php if ($rec['type'] === 'medication'): ?>
                            <i class="bi bi-capsule fs-4 text-primary"></i>
                        <?php elseif ($rec['type'] === 'supplement'): ?>
                            <i class="bi bi-droplet fs-4 text-info"></i>
                        <?php else: ?>
                            <i class="bi bi-heart-pulse fs-4 text-success"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong><?= e($rec['title']) ?></strong>
                        <p class="text-muted small mb-0"><?= e($rec['description']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Milestones Table -->
        <div class="card">
            <div class="card-header">Weekly Milestones</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Week</th>
                                <th>Date</th>
                                <th>Projected Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($milestones as $m): ?>
                            <tr>
                                <td><?= $m['week'] ?></td>
                                <td><?= formatDate($m['date']) ?></td>
                                <td><?= $m['projected_weight'] ?> lbs</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $scripts = '<script>
const milestones = ' . $milestonesJson . ';
const projLabels = milestones.map(m => m.date);
const projData = milestones.map(m => m.projected_weight);

// Fetch actual weight data
fetch("/planner/data")
    .then(r => r.json())
    .then(actual => {
        const actualMap = {};
        actual.forEach(a => actualMap[a.date] = a.weight);
        const actualData = projLabels.map(d => actualMap[d] || null);

        new Chart(document.getElementById("plannerChart"), {
            type: "line",
            data: {
                labels: projLabels.map(d => new Date(d).toLocaleDateString("en-US", {month: "short", day: "numeric"})),
                datasets: [
                    {
                        label: "' . e(__('planner.projected')) . '",
                        data: projData,
                        borderColor: "#6c757d",
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: "' . e(__('planner.actual')) . '",
                        data: actualData,
                        borderColor: "#0d6efd",
                        backgroundColor: "rgba(13,110,253,0.1)",
                        fill: true,
                        tension: 0.3,
                        spanGaps: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: "bottom" } }
            }
        });
    });
</script>'; ?>
