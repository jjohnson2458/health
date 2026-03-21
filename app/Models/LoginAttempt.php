<?php

namespace App\Models;

use Core\Database;
use Core\Encryption;
use Core\Model;

class LoginAttempt extends Model
{
    protected static string $table = 'login_attempts';

    public static function record(string $email, string $ip, bool $success): void
    {
        static::create([
            'email_hash' => Encryption::hashEmail($email),
            'ip_address' => $ip,
            'success' => $success ? 1 : 0,
        ]);
    }

    public static function isRateLimited(string $email, string $ip, int $maxAttempts = 5, int $windowMinutes = 15): bool
    {
        $db = Database::getInstance();
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$windowMinutes} minutes"));
        $emailHash = Encryption::hashEmail($email);

        $stmt = $db->prepare(
            'SELECT COUNT(*) as cnt FROM login_attempts
             WHERE (email_hash = ? OR ip_address = ?)
             AND success = 0
             AND attempted_at > ?'
        );
        $stmt->execute([$emailHash, $ip, $cutoff]);
        $count = (int) $stmt->fetch()['cnt'];

        return $count >= $maxAttempts;
    }
}
