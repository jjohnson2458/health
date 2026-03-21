<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\HealthEntry;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index(): void
    {
        $userId = (int) Session::get('user_id');
        $today = date('Y-m-d');

        $todayEntry = HealthEntry::getByUserAndDate($userId, $today);
        $recentEntries = HealthEntry::getRecentEntries($userId, 7);
        $currentWeight = HealthEntry::getLatestWeight($userId);
        $streak = HealthEntry::getStreak($userId);

        // Calculate 7-day avg calories
        $avgCalories = 0;
        $calorieEntries = array_filter($recentEntries, fn($e) => $e['calories'] > 0);
        if (count($calorieEntries) > 0) {
            $avgCalories = round(array_sum(array_column($calorieEntries, 'calories')) / count($calorieEntries));
        }

        // Prepare chart data
        $chartLabels = [];
        $chartWeight = [];
        $chartCalories = [];
        foreach ($recentEntries as $entry) {
            $chartLabels[] = date('M j', strtotime($entry['entry_date']));
            $chartWeight[] = $entry['weight'] ? (float) $entry['weight'] : null;
            $chartCalories[] = $entry['calories'] ?? null;
        }

        AuditLog::log($userId, 'view', 'dashboard');

        View::render('dashboard/index', [
            'title' => __('dashboard.title'),
            'todayEntry' => $todayEntry,
            'currentWeight' => $currentWeight,
            'avgCalories' => $avgCalories,
            'streak' => $streak,
            'chartLabels' => json_encode($chartLabels),
            'chartWeight' => json_encode($chartWeight),
            'chartCalories' => json_encode($chartCalories),
        ]);
    }
}
