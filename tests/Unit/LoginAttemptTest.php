<?php

namespace Tests\Unit;

use App\Models\LoginAttempt;
use Core\Encryption;
use PHPUnit\Framework\TestCase;

class LoginAttemptTest extends TestCase
{
    public function test_login_attempt_class_exists(): void
    {
        $this->assertTrue(class_exists(LoginAttempt::class));
    }

    public function test_record_method_exists(): void
    {
        $this->assertTrue(method_exists(LoginAttempt::class, 'record'));
    }

    public function test_is_rate_limited_method_exists(): void
    {
        $this->assertTrue(method_exists(LoginAttempt::class, 'isRateLimited'));
    }

    public function test_rate_limit_uses_email_hash(): void
    {
        // Verify the hashing mechanism that isRateLimited relies on
        $email = 'test@example.com';
        $hash = Encryption::hashEmail($email);

        $this->assertEquals(64, strlen($hash));
        // Same email always produces the same hash for lookups
        $this->assertEquals($hash, Encryption::hashEmail($email));
    }

    /**
     * @group integration
     */
    public function test_record_and_rate_limit(): void
    {
        $email = 'ratelimit-test-' . uniqid() . '@example.com';
        $ip = '192.0.2.1';

        // Should not be rate limited initially
        $this->assertFalse(LoginAttempt::isRateLimited($email, $ip, 5, 15));

        // Record 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            LoginAttempt::record($email, $ip, false);
        }

        // Now should be rate limited
        $this->assertTrue(LoginAttempt::isRateLimited($email, $ip, 5, 15));
    }

    /**
     * @group integration
     */
    public function test_successful_login_does_not_count_toward_rate_limit(): void
    {
        $email = 'success-test-' . uniqid() . '@example.com';
        $ip = '192.0.2.2';

        // Record successful attempts - these should not trigger rate limiting
        for ($i = 0; $i < 10; $i++) {
            LoginAttempt::record($email, $ip, true);
        }

        $this->assertFalse(LoginAttempt::isRateLimited($email, $ip, 5, 15));
    }
}
