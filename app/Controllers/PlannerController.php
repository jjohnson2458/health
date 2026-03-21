<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\HealthEntry;
use App\Models\AuditLog;

class PlannerController extends Controller
{
    public function index(): void
    {
        $userId = (int) Session::get('user_id');
        $currentWeight = HealthEntry::getLatestWeight($userId);

        AuditLog::log($userId, 'view', 'planner');

        View::render('planner/index', [
            'title' => __('planner.title'),
            'currentWeight' => $currentWeight,
        ]);
    }

    public function create(): void
    {
        $data = $this->validate([
            'current_weight' => 'required|numeric',
            'goal_weight' => 'required|numeric',
            'weekly_goal' => 'required|numeric',
        ]);

        $targetDate = $this->input('target_date');
        $currentWeight = (float) $data['current_weight'];
        $goalWeight = (float) $data['goal_weight'];
        $weeklyGoal = (float) $data['weekly_goal'];

        if ($weeklyGoal <= 0) {
            $weeklyGoal = 1.0;
        }

        $weightDiff = abs($currentWeight - $goalWeight);
        $weeksNeeded = ceil($weightDiff / $weeklyGoal);

        // Generate projected milestones
        $milestones = [];
        $projectedWeight = $currentWeight;
        $direction = $currentWeight > $goalWeight ? -1 : 1;

        for ($i = 0; $i <= $weeksNeeded; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} weeks"));
            $milestones[] = [
                'week' => $i,
                'date' => $date,
                'projected_weight' => round($projectedWeight, 1),
            ];
            $projectedWeight += $direction * $weeklyGoal;
            if (($direction === -1 && $projectedWeight < $goalWeight) ||
                ($direction === 1 && $projectedWeight > $goalWeight)) {
                $projectedWeight = $goalWeight;
            }
        }

        // BMI-based recommendations
        $recommendations = $this->getMedicationRecommendations($currentWeight, $goalWeight);

        AuditLog::log((int) Session::get('user_id'), 'create', 'planner');

        View::render('planner/results', [
            'title' => __('planner.title'),
            'currentWeight' => $currentWeight,
            'goalWeight' => $goalWeight,
            'weeklyGoal' => $weeklyGoal,
            'weeksNeeded' => $weeksNeeded,
            'milestones' => $milestones,
            'recommendations' => $recommendations,
            'milestonesJson' => json_encode($milestones),
        ]);
    }

    public function data(): void
    {
        $userId = (int) Session::get('user_id');
        $entries = HealthEntry::getEntriesForUser($userId);

        $actual = [];
        foreach ($entries as $entry) {
            if ($entry['weight']) {
                $actual[] = [
                    'date' => $entry['entry_date'],
                    'weight' => (float) $entry['weight'],
                ];
            }
        }

        $this->json($actual);
    }

    private function getMedicationRecommendations(float $currentWeight, float $goalWeight): array
    {
        $recommendations = [];
        $tolose = $currentWeight - $goalWeight;

        if ($tolose > 50) {
            $recommendations[] = [
                'type' => 'medication',
                'title' => 'Consult with Healthcare Provider',
                'description' => 'With a significant weight loss goal, prescription medications like GLP-1 receptor agonists (e.g., semaglutide, tirzepatide) may be beneficial. Always consult your doctor.',
            ];
        }
        if ($tolose > 20) {
            $recommendations[] = [
                'type' => 'supplement',
                'title' => 'Dietary Supplements',
                'description' => 'Consider discussing fiber supplements, protein supplements, and multivitamins with your healthcare provider to support your weight loss journey.',
            ];
        }
        $recommendations[] = [
            'type' => 'lifestyle',
            'title' => 'Lifestyle Modifications',
            'description' => 'Combine calorie tracking with regular exercise (150+ minutes/week moderate activity). Focus on whole foods, adequate protein, and proper hydration.',
        ];

        return $recommendations;
    }
}
