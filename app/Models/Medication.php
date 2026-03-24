<?php

namespace App\Models;

use Core\Database;
use Core\Encryption;
use Core\Model;

class Medication extends Model
{
    protected static string $table = 'medications';

    private static array $encryptedFields = ['name', 'dosage', 'prescriber_name', 'discontinued_reason', 'notes'];

    public static function createMedication(int $userId, array $data): int
    {
        $data['user_id'] = $userId;
        $data = self::encryptFields($data);
        $id = static::create($data);

        MedicationHistory::log($id, $userId, 'added', 'patient', 'Medication added');
        return $id;
    }

    public static function updateMedication(int $id, array $data): bool
    {
        $data = self::encryptFields($data);
        return static::update($id, $data);
    }

    public static function decryptMedication(array $med): array
    {
        foreach (self::$encryptedFields as $field) {
            if (isset($med[$field]) && $med[$field] !== null) {
                $med[$field] = Encryption::decrypt($med[$field]);
            }
        }
        return $med;
    }

    public static function getActiveForUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT * FROM medications WHERE user_id = ? AND status = ? ORDER BY name ASC'
        );
        $stmt->execute([$userId, 'active']);
        return array_map([self::class, 'decryptMedication'], $stmt->fetchAll());
    }

    public static function getAllForUser(int $userId, bool $includeDiscontinued = true): array
    {
        $db = Database::getInstance();
        if ($includeDiscontinued) {
            $stmt = $db->prepare('SELECT * FROM medications WHERE user_id = ? ORDER BY status ASC, name ASC');
            $stmt->execute([$userId]);
        } else {
            $stmt = $db->prepare('SELECT * FROM medications WHERE user_id = ? AND status != ? ORDER BY name ASC');
            $stmt->execute([$userId, 'discontinued']);
        }
        return array_map([self::class, 'decryptMedication'], $stmt->fetchAll());
    }

    public static function discontinue(int $id, int $userId, string $reason = ''): void
    {
        $data = [
            'status' => 'discontinued',
            'discontinued_date' => date('Y-m-d'),
        ];
        if ($reason) {
            $data['discontinued_reason'] = Encryption::encrypt($reason);
        }
        static::update($id, $data);

        MedicationHistory::log($id, $userId, 'discontinued', 'patient', $reason ?: 'No reason given');
    }

    public static function reactivate(int $id, int $userId): void
    {
        static::update($id, [
            'status' => 'active',
            'discontinued_date' => null,
            'discontinued_reason' => null,
        ]);

        MedicationHistory::log($id, $userId, 'reactivated', 'patient', 'Medication reactivated');
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM medications WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $result = $stmt->fetch();
        return $result ? self::decryptMedication($result) : null;
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
