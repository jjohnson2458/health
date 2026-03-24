<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\Appointment;
use App\Models\AuditLog;

class AppointmentController extends Controller
{
    public function index(): void
    {
        $userId = (int) Session::get('user_id');
        $upcoming = Appointment::getUpcomingForUser($userId);
        $past = Appointment::getPastForUser($userId);

        AuditLog::log($userId, 'view', 'appointments', 'Viewed appointments');

        View::render('appointments/index', [
            'title' => __('appointments.title'),
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }

    public function calendar(): void
    {
        $userId = (int) Session::get('user_id');
        $year = (int) ($this->input('year', date('Y')));
        $month = (int) ($this->input('month', date('n')));

        $appointments = Appointment::getForMonth($userId, $year, $month);

        // Group by date for calendar rendering
        $byDate = [];
        foreach ($appointments as $appt) {
            $byDate[$appt['appointment_date']][] = $appt;
        }

        View::render('appointments/calendar', [
            'title' => __('appointments.calendar'),
            'year' => $year,
            'month' => $month,
            'appointments' => $appointments,
            'byDate' => $byDate,
        ]);
    }

    public function create(): void
    {
        View::render('appointments/form', [
            'title' => __('appointments.add'),
            'appointment' => null,
        ]);
    }

    public function store(): void
    {
        $userId = (int) Session::get('user_id');

        $data = $this->validate([
            'appointment_date' => 'required',
            'provider_name' => 'max:255',
        ]);

        $data['appointment_time'] = $this->input('appointment_time') ?: null;
        $data['location'] = $this->input('location');
        $data['type'] = $this->input('type', 'checkup');
        $data['notes'] = $this->input('notes');
        $data['source'] = 'manual';

        Appointment::createAppointment($userId, $data);
        AuditLog::log($userId, 'create', 'appointments', "Added appointment for {$data['appointment_date']}");

        Session::flash('success', __('appointments.saved'));
        $this->redirect('/appointments');
    }

    public function edit(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $appointment = Appointment::findForUser((int) $id, $userId);

        if (!$appointment) {
            $this->redirect('/appointments');
            return;
        }

        View::render('appointments/form', [
            'title' => __('appointments.edit'),
            'appointment' => $appointment,
        ]);
    }

    public function update(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $appointment = Appointment::findForUser((int) $id, $userId);

        if (!$appointment) {
            $this->redirect('/appointments');
            return;
        }

        $data = $this->validate([
            'appointment_date' => 'required',
            'provider_name' => 'max:255',
        ]);

        $data['appointment_time'] = $this->input('appointment_time') ?: null;
        $data['location'] = $this->input('location');
        $data['type'] = $this->input('type', 'checkup');
        $data['notes'] = $this->input('notes');

        Appointment::updateAppointment((int) $id, $data);
        AuditLog::log($userId, 'edit', 'appointments', "Updated appointment #{$id}");

        Session::flash('success', __('appointments.updated'));
        $this->redirect('/appointments');
    }

    public function complete(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $appointment = Appointment::findForUser((int) $id, $userId);

        if (!$appointment) {
            $this->redirect('/appointments');
            return;
        }

        Appointment::markCompleted((int) $id);
        AuditLog::log($userId, 'edit', 'appointments', "Completed appointment #{$id}");

        Session::flash('success', __('appointments.completed'));
        $this->redirect('/appointments');
    }

    public function cancel(string $id): void
    {
        $userId = (int) Session::get('user_id');
        $appointment = Appointment::findForUser((int) $id, $userId);

        if (!$appointment) {
            $this->redirect('/appointments');
            return;
        }

        Appointment::markCancelled((int) $id);
        AuditLog::log($userId, 'edit', 'appointments', "Cancelled appointment #{$id}");

        Session::flash('success', __('appointments.cancelled'));
        $this->redirect('/appointments');
    }
}
