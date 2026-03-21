<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\HealthEntry;
use App\Models\AuditLog;

class AnalyticsController extends Controller
{
    public function index(): void
    {
        AuditLog::log((int) Session::get('user_id'), 'view', 'analytics');
        View::render('dashboard/analytics', ['title' => __('analytics.title')]);
    }

    public function data(): void
    {
        $userId = (int) Session::get('user_id');
        $range = $this->input('range', 'month');

        $startDate = match ($range) {
            'week' => date('Y-m-d', strtotime('-7 days')),
            'month' => date('Y-m-d', strtotime('-30 days')),
            '3months' => date('Y-m-d', strtotime('-90 days')),
            '6months' => date('Y-m-d', strtotime('-180 days')),
            'year' => date('Y-m-d', strtotime('-365 days')),
            default => date('Y-m-d', strtotime('-30 days')),
        };

        $entries = HealthEntry::getEntriesForUser($userId, $startDate);
        $dayOfWeekAvg = HealthEntry::getDayOfWeekAverages($userId, $startDate);

        $data = [
            'labels' => [],
            'weight' => [],
            'calories' => [],
            'heart_rate' => [],
            'blood_sugar' => [],
            'exercise' => [],
            'dayOfWeek' => $dayOfWeekAvg,
        ];

        foreach ($entries as $entry) {
            $data['labels'][] = date('M j', strtotime($entry['entry_date']));
            $data['weight'][] = $entry['weight'] ? (float) $entry['weight'] : null;
            $data['calories'][] = $entry['calories'];
            $data['heart_rate'][] = $entry['heart_rate'];
            $data['blood_sugar'][] = $entry['blood_sugar'] ? (float) $entry['blood_sugar'] : null;
            $data['exercise'][] = $entry['exercise_minutes'];
        }

        $this->json($data);
    }
}
