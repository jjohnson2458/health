<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\Medication;
use App\Models\MedicationHistory;
use App\Models\AuditLog;

class MedicationController extends Controller
{
    public function index(): void
    {
        $userId = (int) Session::get('user_id');
        $medications = Medication::getAllForUser($userId);

        $active = array_filter($medications, fn($m) => $m['status'] === 'active');
        $discontinued = array_filter($medications, fn($m) => $m['status'] === 'discontinued');

        AuditLog::log($userId, 'view', 'medications', 'Viewed medication list');

        View::render('medications/index', [
            'title' => __('medications.title'),
            'active' => array_values($active),
            'discontinued' => array_values($discontinued),
        ]);
    }

    public function create(): void
    {
        View::render('medications/form', [
            'title' => __('medications.add'),
            'medication' => null,
        ]);
    }

    public function store(): void
    {
        $userId = (int) Session::get('user_id');

        $data = $this->validate([
            'name' => 'required|max:255',
            'dosage' => 'max:255',
            'frequency' => 'max:100',
            'prescriber_name' => 'max:255',
        ]);

        $data['prescriber_npi'] = $this->input('prescriber_npi');
        $data['prescribed_date'] = $this->input('prescribed_date') ?: null;
        $data['notes'] = $this->input('notes');
        $data['source'] = 'manual';

        Medication::createMedication($userId, $data);
        AuditLog::log($userId, 'create', 'medications', "Added medication: {$data['name']}");

        Session::flash('success', __('medications.saved'));
        $this->redirect('/medications');
    }

    public function edit(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $medication = Medication::findForUser((int) $id, $userId);

        if (!$medication) {
            $this->redirect('/medications');
            return;
        }

        View::render('medications/form', [
            'title' => __('medications.edit'),
            'medication' => $medication,
        ]);
    }

    public function update(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $medication = Medication::findForUser((int) $id, $userId);

        if (!$medication) {
            $this->redirect('/medications');
            return;
        }

        $data = $this->validate([
            'name' => 'required|max:255',
            'dosage' => 'max:255',
            'frequency' => 'max:100',
            'prescriber_name' => 'max:255',
        ]);

        $data['prescriber_npi'] = $this->input('prescriber_npi');
        $data['prescribed_date'] = $this->input('prescribed_date') ?: null;
        $data['notes'] = $this->input('notes');

        Medication::updateMedication((int) $id, $data);
        MedicationHistory::log((int) $id, $userId, 'updated', 'patient', 'Medication details updated');
        AuditLog::log($userId, 'edit', 'medications', "Updated medication #{$id}");

        Session::flash('success', __('medications.updated'));
        $this->redirect('/medications');
    }

    public function discontinue(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $medication = Medication::findForUser((int) $id, $userId);

        if (!$medication) {
            $this->redirect('/medications');
            return;
        }

        $reason = $this->input('reason', '');
        Medication::discontinue((int) $id, $userId, $reason);
        AuditLog::log($userId, 'edit', 'medications', "Discontinued medication #{$id}: {$reason}");

        Session::flash('success', __('medications.discontinued'));
        $this->redirect('/medications');
    }

    public function reactivate(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $medication = Medication::findForUser((int) $id, $userId);

        if (!$medication) {
            $this->redirect('/medications');
            return;
        }

        Medication::reactivate((int) $id, $userId);
        AuditLog::log($userId, 'edit', 'medications', "Reactivated medication #{$id}");

        Session::flash('success', __('medications.reactivated'));
        $this->redirect('/medications');
    }

    public function history(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $medication = Medication::findForUser((int) $id, $userId);

        if (!$medication) {
            $this->redirect('/medications');
            return;
        }

        $history = MedicationHistory::getForMedication((int) $id);

        View::render('medications/history', [
            'title' => __('medications.history'),
            'medication' => $medication,
            'history' => $history,
        ]);
    }

    public function share(): void
    {
        $userId = (int) Session::get('user_id');
        $medications = Medication::getActiveForUser($userId);
        $userData = Session::get('user_data');

        AuditLog::log($userId, 'export', 'medications', 'Generated shareable medication list');

        View::render('medications/share', [
            'title' => __('medications.share'),
            'medications' => $medications,
            'user' => $userData,
        ]);
    }
}
