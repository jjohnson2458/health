<?php

namespace Tests\Unit;

use Core\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];
    }

    public function test_set_and_get(): void
    {
        Session::set('foo', 'bar');
        $this->assertEquals('bar', Session::get('foo'));
    }

    public function test_get_default_value(): void
    {
        $this->assertNull(Session::get('nonexistent'));
        $this->assertEquals('default', Session::get('nonexistent', 'default'));
    }

    public function test_has(): void
    {
        $this->assertFalse(Session::has('key'));
        Session::set('key', 'value');
        $this->assertTrue(Session::has('key'));
    }

    public function test_remove(): void
    {
        Session::set('key', 'value');
        $this->assertTrue(Session::has('key'));

        Session::remove('key');
        $this->assertFalse(Session::has('key'));
    }

    public function test_flash_and_get_flash(): void
    {
        Session::flash('message', 'Hello');

        // First read returns the value
        $this->assertEquals('Hello', Session::getFlash('message'));

        // Second read returns default (consumed)
        $this->assertNull(Session::getFlash('message'));
    }

    public function test_flash_default_value(): void
    {
        $this->assertEquals('fallback', Session::getFlash('missing', 'fallback'));
    }

    public function test_csrf_token_generation(): void
    {
        $token = Session::generateCsrfToken();

        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex chars

        // Same token returned on subsequent calls
        $this->assertEquals($token, Session::generateCsrfToken());
    }

    public function test_csrf_token_verification(): void
    {
        $token = Session::generateCsrfToken();

        $this->assertTrue(Session::verifyCsrfToken($token));
        $this->assertFalse(Session::verifyCsrfToken('wrong_token'));
    }
}
