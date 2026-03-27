<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Report - <?= htmlspecialchars($userName) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header .subtitle {
            color: #6b7280;
            font-size: 14px;
        }

        .header .date-range {
            margin-top: 8px;
            font-size: 13px;
            color: #374151;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section h2 {
            font-size: 16px;
            color: #1e40af;
            border-bottom: 1px solid #dbeafe;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #eff6ff;
            color: #1e40af;
            font-weight: 600;
            white-space: nowrap;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .no-data {
            color: #9ca3af;
            font-style: italic;
            padding: 10px 0;
        }

        .med-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .med-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .med-discontinued {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .appt-scheduled {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }

        .print-controls {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 8px;
        }

        .print-controls button {
            padding: 10px 30px;
            font-size: 14px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin: 0 5px;
        }

        .print-controls button:hover {
            background: #1d4ed8;
        }

        .print-controls .btn-back {
            background: #6b7280;
        }

        .print-controls .btn-back:hover {
            background: #4b5563;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .summary-card {
            background: #f0f9ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }

        .summary-card .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .summary-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #1e40af;
        }

        @media print {
            .print-controls {
                display: none !important;
            }

            body {
                padding: 0;
                font-size: 10px;
            }

            .header h1 {
                font-size: 20px;
            }

            table {
                font-size: 9px;
            }

            th, td {
                padding: 4px 6px;
            }

            .section {
                page-break-inside: avoid;
            }

            .summary-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @page {
            margin: 1cm;
            size: landscape;
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button onclick="window.print()">Print / Save as PDF</button>
        <button class="btn-back" onclick="history.back()">Back</button>
    </div>

    <div class="header">
        <h1>Health Report</h1>
        <div class="subtitle">VQ Healthy - Personal Health Tracker</div>
        <div class="date-range">
            Patient: <strong><?= htmlspecialchars($userName) ?></strong>
            <?php if ($startDate || $endDate): ?>
                &nbsp;|&nbsp;
                Date Range: <?= $startDate ? htmlspecialchars($startDate) : 'Beginning' ?>
                to <?= $endDate ? htmlspecialchars($endDate) : 'Present' ?>
            <?php else: ?>
                &nbsp;|&nbsp; All Records
            <?php endif; ?>
            &nbsp;|&nbsp; Generated: <?= date('M j, Y g:i A') ?>
        </div>
    </div>

    <!-- Summary -->
    <div class="section">
        <h2>Summary</h2>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Health Entries</div>
                <div class="value"><?= count($entries) ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Active Medications</div>
                <div class="value"><?= count(array_filter($medications, fn($m) => ($m['status'] ?? '') === 'active')) ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Upcoming Appointments</div>
                <div class="value"><?= count($appointments) ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Date Range</div>
                <div class="value" style="font-size: 14px;">
                    <?php if (!empty($entries)): ?>
                        <?= count($entries) ?> days
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Entries -->
    <div class="section">
        <h2>Health Entries</h2>
        <?php if (empty($entries)): ?>
            <p class="no-data">No health entries found for the selected period.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Weight</th>
                        <th>Calories</th>
                        <th>Protein</th>
                        <th>Carbs</th>
                        <th>Fat</th>
                        <th>Heart Rate</th>
                        <th>Blood Sugar</th>
                        <th>Exercise</th>
                        <th>Type</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['entry_date']) ?></td>
                            <td><?= htmlspecialchars($entry['weight'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['calories'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['protein_g'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['carbs_g'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['fat_g'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['heart_rate'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['blood_sugar'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['exercise_minutes'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['exercise_type'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($entry['notes'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Medications -->
    <div class="section">
        <h2>Medications</h2>
        <?php if (empty($medications)): ?>
            <p class="no-data">No medications on record.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Medication</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Prescriber</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medications as $med): ?>
                        <tr>
                            <td><?= htmlspecialchars($med['name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($med['dosage'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($med['frequency'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($med['prescriber_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($med['start_date'] ?? '-') ?></td>
                            <td>
                                <span class="med-status <?= ($med['status'] ?? '') === 'active' ? 'med-active' : 'med-discontinued' ?>">
                                    <?= htmlspecialchars($med['status'] ?? 'unknown') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($med['notes'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Upcoming Appointments -->
    <div class="section">
        <h2>Upcoming Appointments</h2>
        <?php if (empty($appointments)): ?>
            <p class="no-data">No upcoming appointments scheduled.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Provider</th>
                        <th>Location</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td><?= htmlspecialchars($appt['appointment_date'] ?? '-') ?></td>
                            <td><?= isset($appt['appointment_time']) ? date('g:i A', strtotime($appt['appointment_time'])) : '-' ?></td>
                            <td><?= htmlspecialchars($appt['appointment_type'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($appt['provider_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($appt['location'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($appt['notes'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>This report was generated from VQ Healthy on <?= date('F j, Y \a\t g:i A') ?>.</p>
        <p>This document contains protected health information (PHI). Handle in accordance with HIPAA regulations.</p>
    </div>
</body>
</html>
