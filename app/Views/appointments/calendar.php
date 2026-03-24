<?php
$monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfWeek = (int) date('w', mktime(0, 0, 0, $month, 1, $year)); // 0=Sun
$prevMonth = $month === 1 ? 12 : $month - 1;
$prevYear = $month === 1 ? $year - 1 : $year;
$nextMonth = $month === 12 ? 1 : $month + 1;
$nextYear = $month === 12 ? $year + 1 : $year;
$today = date('Y-m-d');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-calendar3"></i> <?= e(__('appointments.calendar')) ?></h4>
    <div>
        <a href="/appointments" class="btn btn-outline-secondary btn-sm me-1">
            <i class="bi bi-list-ul"></i> <?= e(__('appointments.list_view')) ?>
        </a>
        <a href="/appointments/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> <?= e(__('appointments.add')) ?>
        </a>
    </div>
</div>

<!-- Month Navigation -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="/appointments/calendar?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-chevron-left"></i>
    </a>
    <h5 class="mb-0"><?= e($monthName) ?> <?= $year ?></h5>
    <a href="/appointments/calendar?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-chevron-right"></i>
    </a>
</div>

<!-- Calendar Grid -->
<div class="card shadow-sm">
    <div class="card-body p-2">
        <table class="table table-bordered mb-0" style="table-layout:fixed;">
            <thead class="table-light">
                <tr>
                    <th class="text-center small">Sun</th>
                    <th class="text-center small">Mon</th>
                    <th class="text-center small">Tue</th>
                    <th class="text-center small">Wed</th>
                    <th class="text-center small">Thu</th>
                    <th class="text-center small">Fri</th>
                    <th class="text-center small">Sat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $day = 1;
                $totalCells = $firstDayOfWeek + $daysInMonth;
                $rows = ceil($totalCells / 7);
                for ($row = 0; $row < $rows; $row++): ?>
                <tr>
                    <?php for ($col = 0; $col < 7; $col++):
                        $cellIndex = $row * 7 + $col;
                        if ($cellIndex < $firstDayOfWeek || $day > $daysInMonth): ?>
                            <td class="bg-light" style="height:80px;"></td>
                        <?php else:
                            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                            $isToday = $dateStr === $today;
                            $hasAppts = isset($byDate[$dateStr]);
                        ?>
                            <td class="<?= $isToday ? 'bg-primary bg-opacity-10' : '' ?>" style="height:80px;vertical-align:top;">
                                <div class="d-flex justify-content-between">
                                    <span class="small <?= $isToday ? 'fw-bold text-primary' : '' ?>"><?= $day ?></span>
                                </div>
                                <?php if ($hasAppts):
                                    foreach ($byDate[$dateStr] as $appt): ?>
                                        <a href="/appointments/<?= $appt['id'] ?>" class="d-block small text-truncate text-decoration-none mt-1"
                                           title="<?= e($appt['provider_name'] ?? ucfirst($appt['type'])) ?>">
                                            <span class="badge bg-primary w-100 text-start text-truncate">
                                                <?php if ($appt['appointment_time']): ?>
                                                    <?= e(date('g:iA', strtotime($appt['appointment_time']))) ?>
                                                <?php endif; ?>
                                                <?= e($appt['provider_name'] ?? ucfirst($appt['type'])) ?>
                                            </span>
                                        </a>
                                    <?php endforeach;
                                endif; ?>
                            </td>
                        <?php
                            $day++;
                        endif;
                    endfor; ?>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>
