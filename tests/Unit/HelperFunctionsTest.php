<?php

namespace Tests\Unit;

use Core\Session;
use PHPUnit\Framework\TestCase;

class HelperFunctionsTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_e_escapes_html_entities(): void
    {
        $this->assertEquals('&lt;script&gt;alert(1)&lt;/script&gt;', e('<script>alert(1)</script>'));
        $this->assertEquals('&amp;', e('&'));
        $this->assertEquals('&quot;', e('"'));
        $this->assertEquals('&#039;', e("'"));
    }

    public function test_e_handles_null(): void
    {
        $this->assertEquals('', e(null));
    }

    public function test_e_handles_empty_string(): void
    {
        $this->assertEquals('', e(''));
    }

    public function test_e_passes_through_safe_strings(): void
    {
        $this->assertEquals('Hello World', e('Hello World'));
    }

    public function test_url_generates_path(): void
    {
        $_ENV['APP_URL'] = 'https://health.local';

        $this->assertEquals('https://health.local/', url());
        $this->assertEquals('https://health.local/login', url('login'));
        $this->assertEquals('https://health.local/dashboard', url('/dashboard'));
    }

    public function test_url_handles_trailing_slash_on_base(): void
    {
        $_ENV['APP_URL'] = 'https://health.local/';

        $this->assertEquals('https://health.local/login', url('login'));
    }

    public function test_url_handles_empty_app_url(): void
    {
        $_ENV['APP_URL'] = '';

        $this->assertEquals('/login', url('login'));
    }

    public function test_format_date_default_format(): void
    {
        $this->assertEquals('Jan 15, 2025', formatDate('2025-01-15'));
    }

    public function test_format_date_custom_format(): void
    {
        $this->assertEquals('2025-01-15', formatDate('2025-01-15', 'Y-m-d'));
        $this->assertEquals('01/15/2025', formatDate('2025-01-15', 'm/d/Y'));
    }

    public function test_format_date_with_datetime_string(): void
    {
        $this->assertEquals('Mar 23, 2026', formatDate('2026-03-23 14:30:00'));
    }

    public function test_csrf_field_outputs_hidden_input(): void
    {
        $html = csrf_field();

        $this->assertStringContainsString('<input type="hidden"', $html);
        $this->assertStringContainsString('name="_csrf_token"', $html);
        $this->assertStringContainsString('value="', $html);

        // Token should be 64 hex chars
        preg_match('/value="([^"]+)"/', $html, $matches);
        $this->assertNotEmpty($matches[1]);
        $this->assertEquals(64, strlen($matches[1]));
    }

    public function test_csrf_field_uses_session_token(): void
    {
        $token = Session::generateCsrfToken();
        $html = csrf_field();

        $this->assertStringContainsString($token, $html);
    }
}
