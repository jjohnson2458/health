<?php

namespace App\Models;

use Core\Database;
use Core\Model;

class MedicationHistory extends Model
{
    protected static string $table = 'medication_history';

    public static function log(
        int $medicationId,
        int $userId,
        string $action,
        string $changedBy = 'patient',
        ?string $details = null
    ): int {
        return static::create([
            'medication_id' => $medicationId,
            'user_id' => $userId,
            'action' => $action,
            'changed_by' => $changedBy,
            'details' => $details,
        ]);
    }

    public static function getForMedication(int $medicationId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT * FROM medication_history WHERE medication_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$medicationId]);
        return $stmt->fetchAll();
    }
}
