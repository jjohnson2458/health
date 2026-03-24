<?php

namespace Tests\Unit;

use Core\Mailer;
use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase
{
    public function test_mailer_class_exists(): void
    {
        $this->assertTrue(class_exists(\Core\Mailer::class));
    }

    public function test_send_method_exists(): void
    {
        $this->assertTrue(method_exists(\Core\Mailer::class, 'send'));
    }

    public function test_send_returns_false_when_no_smtp_and_no_messenger(): void
    {
        // With no SMTP_HOST and no valid MAIL_NOTIFY_SCRIPT, send should return false
        $originalHost = $_ENV['SMTP_HOST'] ?? '';
        $originalScript = $_ENV['MAIL_NOTIFY_SCRIPT'] ?? '';

        $_ENV['SMTP_HOST'] = '';
        $_ENV['MAIL_NOTIFY_SCRIPT'] = '/nonexistent/path/notify.php';

        $result = Mailer::send('test@example.com', 'Test', '<p>Test</p>');
        $this->assertFalse($result);

        $_ENV['SMTP_HOST'] = $originalHost;
        $_ENV['MAIL_NOTIFY_SCRIPT'] = $originalScript;
    }
}
