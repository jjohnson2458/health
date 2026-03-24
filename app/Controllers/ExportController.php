<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use App\Models\HealthEntry;
use App\Models\Medication;
use App\Models\Appointment;
use App\Models\User;
use App\Models\AuditLog;

class ExportController extends Controller
{
    public function csv(): void
    {
        $userId = (int) Session::get('user_id');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        $entries = HealthEntry::getEntriesForUser($userId, $startDate, $endDate);

        AuditLog::log($userId, 'export', 'health_entries', 'CSV export: ' . count($entries) . ' entries');

        $filename = 'health-data-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, [
            'Date', 'Weight (lbs)', 'Calories', 'Protein (g)', 'Carbs (g)', 'Fat (g)',
            'Heart Rate (bpm)', 'Blood Sugar (mg/dL)', 'Exercise (min)', 'Exercise Type', 'Notes'
        ]);

        foreach ($entries as $entry) {
            fputcsv($output, [
                $entry['entry_date'],
                $entry['weight'] ?? '',
                $entry['calories'] ?? '',
                $entry['protein_g'] ?? '',
                $entry['carbs_g'] ?? '',
                $entry['fat_g'] ?? '',
                $entry['heart_rate'] ?? '',
                $entry['blood_sugar'] ?? '',
                $entry['exercise_minutes'] ?? '',
                $entry['exercise_type'] ?? '',
                $entry['notes'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }

    public function printView(): void
    {
        $userId = (int) Session::get('user_id');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        // Get user info
        $user = User::find($userId);
        $user = User::decryptUser($user);
        $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

        // Get health entries
        $entries = HealthEntry::getEntriesForUser($userId, $startDate, $endDate);

        // Get medications (all, including discontinued)
        $medications = Medication::getAllForUser($userId);

        // Get upcoming appointments
        $appointments = Appointment::getUpcomingForUser($userId);

        AuditLog::log($userId, 'export', 'health_entries', 'Print export: ' . count($entries) . ' entries');

        require __DIR__ . '/../Views/export/print.php';
        exit;
    }

    public function pdf(): void
    {
        // Redirect to the printable view - user can use browser's Print > Save as PDF
        $params = [];
        if ($startDate = $this->input('start_date')) {
            $params['start_date'] = $startDate;
        }
        if ($endDate = $this->input('end_date')) {
            $params['end_date'] = $endDate;
        }
        $query = $params ? '?' . http_build_query($params) : '';
        header('Location: /export/print' . $query);
        exit;
    }
}
