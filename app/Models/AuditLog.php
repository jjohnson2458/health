<?php

namespace App\Models;

use Core\Database;
use Core\Model;

class AuditLog extends Model
{
    protected static string $table = 'audit_log';

    public static function log(?int $userId, string $action, ?string $resource = null, ?string $details = null): void
    {
        static::create([
            'user_id' => $userId,
            'action' => $action,
            'resource' => $resource,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ]);
    }
}
