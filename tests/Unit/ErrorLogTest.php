<?php

namespace Tests\Unit;

use App\Models\ErrorLog;
use PHPUnit\Framework\TestCase;

class ErrorLogTest extends TestCase
{
    public function test_error_log_class_exists(): void
    {
        $this->assertTrue(class_exists(ErrorLog::class));
    }

    public function test_capture_method_exists(): void
    {
        $this->assertTrue(method_exists(ErrorLog::class, 'capture'));
    }

    public function test_capture_does_not_throw_on_db_failure(): void
    {
        // ErrorLog::capture should fail gracefully even if DB is not available
        // It falls back to error_log() instead of throwing
        $originalHost = $_ENV['DB_HOST'] ?? '';
        $_ENV['DB_HOST'] = 'invalid_host_that_does_not_exist';

        // This should not throw
        ErrorLog::capture('ERROR', 'Test error message', __FILE__, __LINE__);
        $this->assertTrue(true); // If we get here, no exception was thrown

        $_ENV['DB_HOST'] = $originalHost;
    }
}
