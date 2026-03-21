<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\AuditLog;

class CalculatorController extends Controller
{
    public function calories(): void
    {
        AuditLog::log((int) Session::get('user_id'), 'view', 'calorie_calculator');
        View::render('calculators/calories', [
            'title' => __('calc.calorie_title'),
            'result' => null,
        ]);
    }

    public function calculateCalories(): void
    {
        $data = $this->validate([
            'age' => 'required|numeric',
            'gender' => 'required',
            'weight' => 'required|numeric',
            'feet' => 'required|numeric',
            'inches' => 'required|numeric',
            'activity_level' => 'required',
            'goal' => 'required',
        ]);

        $weightKg = (float) $data['weight'] * 0.453592;
        $heightCm = ((float) $data['feet'] * 12 + (float) $data['inches']) * 2.54;
        $age = (int) $data['age'];

        // Mifflin-St Jeor
        if ($data['gender'] === 'male') {
            $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) + 5;
        } else {
            $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) - 161;
        }

        $activityMultipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'extra_active' => 1.9,
        ];

        $tdee = $bmr * ($activityMultipliers[$data['activity_level']] ?? 1.2);

        $goalCalories = match ($data['goal']) {
            'lose' => $tdee - 500,
            'gain' => $tdee + 300,
            default => $tdee,
        };

        $result = [
            'bmr' => round($bmr),
            'tdee' => round($tdee),
            'recommended' => round($goalCalories),
            'deficit' => round($tdee - $goalCalories),
        ];

        AuditLog::log((int) Session::get('user_id'), 'calculate', 'calorie_calculator');

        View::render('calculators/calories', [
            'title' => __('calc.calorie_title'),
            'result' => $result,
            'input' => $data,
        ]);
    }

    public function macros(): void
    {
        AuditLog::log((int) Session::get('user_id'), 'view', 'macro_calculator');
        View::render('calculators/macros', [
            'title' => __('calc.macro_title'),
            'result' => null,
        ]);
    }

    public function calculateMacros(): void
    {
        $data = $this->validate([
            'calories' => 'required|numeric',
            'goal' => 'required',
        ]);

        $calories = (int) $data['calories'];

        // Macro splits based on goal
        $splits = match ($data['goal']) {
            'lose' => ['protein' => 0.40, 'carbs' => 0.30, 'fat' => 0.30],
            'gain' => ['protein' => 0.30, 'carbs' => 0.45, 'fat' => 0.25],
            default => ['protein' => 0.30, 'carbs' => 0.40, 'fat' => 0.30],
        };

        $result = [
            'protein_cal' => round($calories * $splits['protein']),
            'carbs_cal' => round($calories * $splits['carbs']),
            'fat_cal' => round($calories * $splits['fat']),
            'protein_g' => round(($calories * $splits['protein']) / 4),
            'carbs_g' => round(($calories * $splits['carbs']) / 4),
            'fat_g' => round(($calories * $splits['fat']) / 9),
            'splits' => $splits,
        ];

        AuditLog::log((int) Session::get('user_id'), 'calculate', 'macro_calculator');

        View::render('calculators/macros', [
            'title' => __('calc.macro_title'),
            'result' => $result,
            'input' => $data,
        ]);
    }
}
