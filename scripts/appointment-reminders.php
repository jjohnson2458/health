<?php
/**
 * Appointment Reminder Script
 * Sends email (and SMS if configured) reminders 24 hours before appointments.
 * Run via cron: 0 9 * * * php /path/to/scripts/appointment-reminders.php
 */

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($projectRoot);
$dotenv->load();

require_once $projectRoot . '/app/Helpers/functions.php';

use App\Models\Appointment;
use Core\Encryption;
use Core\Mailer;

$reminders = Appointment::getRemindersToSend();
$sent = 0;

foreach ($reminders as $reminder) {
    $email = Encryption::decrypt($reminder['email']);
    $firstName = Encryption::decrypt($reminder['first_name']);
    $appt = Appointment::decryptAppointment($reminder);

    $timeStr = $reminder['appointment_time']
        ? date('g:i A', strtotime($reminder['appointment_time']))
        : 'Time not specified';

    $subject = __('app_name') . ' - Appointment Reminder';
    $body = "<h2>" . e($firstName) . ", you have an appointment tomorrow!</h2>"
        . "<p><strong>Date:</strong> " . e(formatDate($reminder['appointment_date'], 'l, F j, Y')) . "</p>"
        . "<p><strong>Time:</strong> " . e($timeStr) . "</p>"
        . ($appt['provider_name'] ? "<p><strong>Provider:</strong> " . e($appt['provider_name']) . "</p>" : '')
        . ($appt['location'] ? "<p><strong>Location:</strong> " . e($appt['location']) . "</p>" : '')
        . ($appt['notes'] ? "<p><strong>Notes:</strong> " . e($appt['notes']) . "</p>" : '')
        . "<p style=\"color:#666;font-size:12px;\">" . e(__('hipaa_notice')) . "</p>";

    if (Mailer::send($email, $subject, $body, 'claude_health')) {
        Appointment::update($reminder['id'], ['email_reminder_sent' => 1]);
        $sent++;
    }
}

echo "Sent {$sent} appointment reminder(s).\n";
