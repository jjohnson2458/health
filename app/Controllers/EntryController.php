<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\HealthEntry;
use App\Models\AuditLog;

class EntryController extends Controller
{
    public function index(): void
    {
        $userId = (int) Session::get('user_id');
        $page = max(1, (int) ($this->input('page', 1)));

        $result = HealthEntry::paginateForUser($userId, $page, 15);

        View::render('entries/index', [
            'title' => __('entry.history_title'),
            'entries' => $result['data'],
            'pagination' => $result,
        ]);
    }

    public function create(): void
    {
        $userId = (int) Session::get('user_id');
        $date = $this->input('date', date('Y-m-d'));

        // Check if entry exists for this date
        $existing = HealthEntry::getByUserAndDate($userId, $date);
        if ($existing) {
            $this->redirect('/entry/' . $existing['id']);
            return;
        }

        View::render('entries/form', [
            'title' => __('entry.title'),
            'entry' => null,
            'date' => $date,
        ]);
    }

    public function store(): void
    {
        $userId = (int) Session::get('user_id');

        $data = $this->validate([
            'entry_date' => 'required',
            'weight' => 'numeric',
            'calories' => 'numeric',
            'protein_g' => 'numeric',
            'carbs_g' => 'numeric',
            'fat_g' => 'numeric',
            'heart_rate' => 'numeric',
            'blood_sugar' => 'numeric',
            'exercise_minutes' => 'numeric',
        ]);

        $data['exercise_type'] = $this->input('exercise_type');
        $data['notes'] = $this->input('notes');

        // Convert from user's unit system to storage units (US)
        if (!empty($data['weight'])) {
            $data['weight'] = inputToLbs((float) $data['weight']);
        }
        if (!empty($data['blood_sugar'])) {
            $data['blood_sugar'] = inputToMgdl((float) $data['blood_sugar']);
        }

        // Check for existing entry on this date
        $existing = HealthEntry::getByUserAndDate($userId, $data['entry_date']);
        if ($existing) {
            HealthEntry::updateEntry($existing['id'], $data);
            AuditLog::log($userId, 'edit', 'health_entries', "Updated entry for {$data['entry_date']}");
            Session::flash('success', __('entry.updated'));
        } else {
            HealthEntry::createEntry($userId, $data);
            AuditLog::log($userId, 'create', 'health_entries', "Created entry for {$data['entry_date']}");
            Session::flash('success', __('entry.saved'));
        }

        $this->redirect('/dashboard');
    }

    public function edit(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $entry = HealthEntry::find((int) $id);

        if (!$entry || (int) $entry['user_id'] !== $userId) {
            $this->redirect('/dashboard');
            return;
        }

        $entry = HealthEntry::decryptEntry($entry);
        AuditLog::log($userId, 'view', 'health_entries', "Viewed entry #{$id}");

        View::render('entries/form', [
            'title' => __('entry.title'),
            'entry' => $entry,
            'date' => $entry['entry_date'],
        ]);
    }

    public function update(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $entry = HealthEntry::find((int) $id);

        if (!$entry || (int) $entry['user_id'] !== $userId) {
            $this->redirect('/dashboard');
            return;
        }

        $data = $this->validate([
            'entry_date' => 'required',
            'weight' => 'numeric',
            'calories' => 'numeric',
            'protein_g' => 'numeric',
            'carbs_g' => 'numeric',
            'fat_g' => 'numeric',
            'heart_rate' => 'numeric',
            'blood_sugar' => 'numeric',
            'exercise_minutes' => 'numeric',
        ]);

        $data['exercise_type'] = $this->input('exercise_type');
        $data['notes'] = $this->input('notes');

        // Convert from user's unit system to storage units (US)
        if (!empty($data['weight'])) {
            $data['weight'] = inputToLbs((float) $data['weight']);
        }
        if (!empty($data['blood_sugar'])) {
            $data['blood_sugar'] = inputToMgdl((float) $data['blood_sugar']);
        }

        HealthEntry::updateEntry((int) $id, $data);
        AuditLog::log($userId, 'edit', 'health_entries', "Updated entry #{$id}");

        Session::flash('success', __('entry.updated'));
        $this->redirect('/dashboard');
    }

    public function delete(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $entry = HealthEntry::find((int) $id);

        if (!$entry || (int) $entry['user_id'] !== $userId) {
            $this->redirect('/dashboard');
            return;
        }

        HealthEntry::delete((int) $id);
        AuditLog::log($userId, 'delete', 'health_entries', "Deleted entry #{$id}");

        Session::flash('success', __('entry.deleted'));
        $this->redirect('/dashboard');
    }
}
