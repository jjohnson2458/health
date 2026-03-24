<?php

namespace App\Models;

use Core\Database;
use Core\Session;

class ErrorLog
{
    public static function capture(
        string $level,
        string $message,
        ?string $file = null,
        ?int $line = null,
        ?string $stackTrace = null
    ): void {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'INSERT INTO error_log (error_level, message, file, line, stack_trace, user_id, ip_address, url)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $level,
                mb_substr($message, 0, 65535),
                $file,
                $line,
                $stackTrace ? mb_substr($stackTrace, 0, 65535) : null,
                Session::get('user_id'),
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['REQUEST_URI'] ?? '',
            ]);
        } catch (\Throwable $e) {
            // Fallback to PHP error log if DB write fails
            error_log("ErrorLog::capture failed: " . $e->getMessage());
        }
    }
}
