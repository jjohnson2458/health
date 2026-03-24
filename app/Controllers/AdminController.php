<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;
use Core\Encryption;
use Core\Session;
use Core\View;
use App\Models\User;
use App\Models\HealthEntry;
use App\Models\Medication;
use App\Models\Appointment;
use App\Models\AuditLog;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        $db = Database::getInstance();

        $totalUsers = $db->query('SELECT COUNT(*) as cnt FROM users')->fetch()['cnt'];
        $totalEntries = $db->query('SELECT COUNT(*) as cnt FROM health_entries')->fetch()['cnt'];
        $totalMeds = $db->query('SELECT COUNT(*) as cnt FROM medications')->fetch()['cnt'];
        $totalAppts = $db->query('SELECT COUNT(*) as cnt FROM appointments')->fetch()['cnt'];
        $recentErrors = $db->query('SELECT COUNT(*) as cnt FROM error_log WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)')->fetch()['cnt'];

        View::render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'totalUsers' => $totalUsers,
            'totalEntries' => $totalEntries,
            'totalMeds' => $totalMeds,
            'totalAppts' => $totalAppts,
            'recentErrors' => $recentErrors,
        ]);
    }

    public function users(): void
    {
        $db = Database::getInstance();
        $page = max(1, (int) ($this->input('page', 1)));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = $db->query('SELECT COUNT(*) as cnt FROM users')->fetch()['cnt'];
        $stmt = $db->prepare('SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->execute([$perPage, $offset]);
        $users = $stmt->fetchAll();

        $decryptedUsers = array_map(function ($u) {
            $u['first_name'] = Encryption::decrypt($u['first_name']);
            $u['last_name'] = Encryption::decrypt($u['last_name']);
            $u['email'] = Encryption::decrypt($u['email']);
            return $u;
        }, $users);

        View::render('admin/users', [
            'title' => 'Manage Users',
            'users' => $decryptedUsers,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'lastPage' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    public function editUser(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user) {
            $this->redirect('/admin/users');
            return;
        }

        $user = User::decryptUser($user);

        View::render('admin/user-edit', [
            'title' => 'Edit User',
            'editUser' => $user,
        ]);
    }

    public function updateUser(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user) {
            $this->redirect('/admin/users');
            return;
        }

        $data = [];
        $role = $this->input('role');
        if ($role && in_array($role, ['user', 'admin'])) {
            $data['role'] = $role;
        }

        $verified = $this->input('email_verified');
        if ($verified !== null) {
            $data['email_verified'] = (int) $verified;
        }

        $language = $this->input('language');
        if ($language && in_array($language, ['en', 'es'])) {
            $data['language'] = $language;
        }

        // Password reset by admin
        $newPassword = $this->input('new_password');
        if ($newPassword && strlen($newPassword) >= 8) {
            $data['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        if (!empty($data)) {
            User::update((int) $id, $data);
            AuditLog::log(
                (int) Session::get('user_id'),
                'admin_edit_user',
                'users',
                "Admin updated user #{$id}: " . implode(', ', array_keys($data))
            );
        }

        Session::flash('success', 'User updated.');
        $this->redirect('/admin/users');
    }

    public function deleteUser(string $id): void
    {
        $adminId = (int) Session::get('user_id');
        if ((int) $id === $adminId) {
            Session::flash('errors', ['Cannot delete your own account.']);
            $this->redirect('/admin/users');
            return;
        }

        User::delete((int) $id);
        AuditLog::log($adminId, 'admin_delete_user', 'users', "Deleted user #{$id}");

        Session::flash('success', 'User deleted.');
        $this->redirect('/admin/users');
    }

    public function exportUserData(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user) {
            $this->redirect('/admin/users');
            return;
        }

        $decryptedUser = User::decryptUser($user);
        $entries = HealthEntry::getEntriesForUser((int) $id);
        $medications = Medication::getAllForUser((int) $id);
        $appointments = Appointment::getUpcomingForUser((int) $id);
        $pastAppts = Appointment::getPastForUser((int) $id);

        AuditLog::log(
            (int) Session::get('user_id'),
            'admin_export',
            'users',
            "Admin exported data for user #{$id}"
        );

        $filename = 'patient-' . $decryptedUser['last_name'] . '-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Patient info
        fputcsv($output, ['PATIENT INFORMATION']);
        fputcsv($output, ['Name', $decryptedUser['first_name'] . ' ' . $decryptedUser['last_name']]);
        fputcsv($output, ['Email', $decryptedUser['email']]);
        fputcsv($output, ['Registered', $user['created_at']]);
        fputcsv($output, []);

        // Health entries
        fputcsv($output, ['HEALTH ENTRIES']);
        fputcsv($output, ['Date', 'Weight', 'Calories', 'Protein', 'Carbs', 'Fat', 'Heart Rate', 'Blood Sugar', 'Exercise Min', 'Exercise Type', 'Notes']);
        foreach ($entries as $entry) {
            fputcsv($output, [
                $entry['entry_date'], $entry['weight'] ?? '', $entry['calories'] ?? '',
                $entry['protein_g'] ?? '', $entry['carbs_g'] ?? '', $entry['fat_g'] ?? '',
                $entry['heart_rate'] ?? '', $entry['blood_sugar'] ?? '',
                $entry['exercise_minutes'] ?? '', $entry['exercise_type'] ?? '', $entry['notes'] ?? '',
            ]);
        }
        fputcsv($output, []);

        // Medications
        fputcsv($output, ['MEDICATIONS']);
        fputcsv($output, ['Name', 'Dosage', 'Frequency', 'Prescriber', 'Prescribed Date', 'Status']);
        foreach ($medications as $med) {
            fputcsv($output, [
                $med['name'], $med['dosage'] ?? '', $med['frequency'] ?? '',
                $med['prescriber_name'] ?? '', $med['prescribed_date'] ?? '', $med['status'],
            ]);
        }
        fputcsv($output, []);

        // Appointments
        fputcsv($output, ['APPOINTMENTS']);
        fputcsv($output, ['Date', 'Time', 'Provider', 'Type', 'Location', 'Status']);
        $allAppts = array_merge($appointments, $pastAppts);
        foreach ($allAppts as $appt) {
            fputcsv($output, [
                $appt['appointment_date'], $appt['appointment_time'] ?? '',
                $appt['provider_name'] ?? '', $appt['type'], $appt['location'] ?? '', $appt['status'],
            ]);
        }

        fclose($output);
        exit;
    }

    public function errors(): void
    {
        $db = Database::getInstance();
        $page = max(1, (int) ($this->input('page', 1)));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $total = $db->query('SELECT COUNT(*) as cnt FROM error_log')->fetch()['cnt'];
        $stmt = $db->prepare('SELECT * FROM error_log ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->execute([$perPage, $offset]);
        $errors = $stmt->fetchAll();

        View::render('admin/errors', [
            'title' => 'Error Log',
            'errors' => $errors,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'lastPage' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }
}
