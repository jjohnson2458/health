<?php

namespace App\Models;

use Core\Database;
use Core\Encryption;
use Core\Model;

class Appointment extends Model
{
    protected static string $table = 'appointments';

    private static array $encryptedFields = ['provider_name', 'location', 'notes'];

    public static function createAppointment(int $userId, array $data): int
    {
        $data['user_id'] = $userId;
        $data = self::encryptFields($data);
        return static::create($data);
    }

    public static function updateAppointment(int $id, array $data): bool
    {
        $data = self::encryptFields($data);
        return static::update($id, $data);
    }

    public static function decryptAppointment(array $appt): array
    {
        foreach (self::$encryptedFields as $field) {
            if (isset($appt[$field]) && $appt[$field] !== null) {
                $appt[$field] = Encryption::decrypt($appt[$field]);
            }
        }
        return $appt;
    }

    public static function getUpcomingForUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT * FROM appointments WHERE user_id = ? AND status = ? AND appointment_date >= CURDATE() ORDER BY appointment_date ASC, appointment_time ASC'
        );
        $stmt->execute([$userId, 'scheduled']);
        return array_map([self::class, 'decryptAppointment'], $stmt->fetchAll());
    }

    public static function getPastForUser(int $userId, int $limit = 20): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT * FROM appointments WHERE user_id = ? AND (status != ? OR appointment_date < CURDATE()) ORDER BY appointment_date DESC LIMIT ?'
        );
        $stmt->execute([$userId, 'scheduled', $limit]);
        return array_map([self::class, 'decryptAppointment'], $stmt->fetchAll());
    }

    public static function getForMonth(int $userId, int $year, int $month): array
    {
        $db = Database::getInstance();
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $stmt = $db->prepare(
            'SELECT * FROM appointments WHERE user_id = ? AND appointment_date BETWEEN ? AND ? ORDER BY appointment_date ASC, appointment_time ASC'
        );
        $stmt->execute([$userId, $startDate, $endDate]);
        return array_map([self::class, 'decryptAppointment'], $stmt->fetchAll());
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM appointments WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $result = $stmt->fetch();
        return $result ? self::decryptAppointment($result) : null;
    }

    public static function markCompleted(int $id): bool
    {
        return static::update($id, ['status' => 'completed']);
    }

    public static function markCancelled(int $id): bool
    {
        return static::update($id, ['status' => 'cancelled']);
    }

    public static function getRemindersToSend(): array
    {
        $db = Database::getInstance();
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $stmt = $db->prepare(
            'SELECT a.*, u.email, u.first_name, u.phone_number, u.sms_opt_in
             FROM appointments a
             JOIN users u ON a.user_id = u.id
             WHERE a.appointment_date = ?
             AND a.status = ?
             AND a.email_reminder_sent = 0'
        );
        $stmt->execute([$tomorrow, 'scheduled']);
        return $stmt->fetchAll();
    }

    private static function encryptFields(array $data): array
    {
        foreach (self::$encryptedFields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $data[$field] = Encryption::encrypt((string) $data[$field]);
            }
        }
        return $data;
    }
}
