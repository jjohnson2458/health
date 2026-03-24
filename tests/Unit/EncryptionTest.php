<?php

namespace Tests\Unit;

use Core\Encryption;
use PHPUnit\Framework\TestCase;

class EncryptionTest extends TestCase
{
    public function test_encrypt_decrypt_roundtrip(): void
    {
        $plaintext = 'John Doe';
        $encrypted = Encryption::encrypt($plaintext);

        $this->assertNotEquals($plaintext, $encrypted);
        $this->assertEquals($plaintext, Encryption::decrypt($encrypted));
    }

    public function test_encrypt_null_returns_null(): void
    {
        $this->assertNull(Encryption::encrypt(null));
        $this->assertNull(Encryption::decrypt(null));
    }

    public function test_encrypt_empty_string_returns_empty(): void
    {
        $this->assertEquals('', Encryption::encrypt(''));
        $this->assertEquals('', Encryption::decrypt(''));
    }

    public function test_encrypt_produces_different_ciphertext_each_time(): void
    {
        $plaintext = 'test data';
        $encrypted1 = Encryption::encrypt($plaintext);
        $encrypted2 = Encryption::encrypt($plaintext);

        // Different IVs mean different ciphertexts
        $this->assertNotEquals($encrypted1, $encrypted2);

        // But both decrypt to the same value
        $this->assertEquals($plaintext, Encryption::decrypt($encrypted1));
        $this->assertEquals($plaintext, Encryption::decrypt($encrypted2));
    }

    public function test_hash_email_is_consistent(): void
    {
        $email = 'Test@Example.com';
        $hash1 = Encryption::hashEmail($email);
        $hash2 = Encryption::hashEmail($email);

        $this->assertEquals($hash1, $hash2);
        $this->assertEquals(64, strlen($hash1));
    }

    public function test_hash_email_is_case_insensitive(): void
    {
        $hash1 = Encryption::hashEmail('User@EXAMPLE.com');
        $hash2 = Encryption::hashEmail('user@example.com');

        $this->assertEquals($hash1, $hash2);
    }

    public function test_hash_email_trims_whitespace(): void
    {
        $hash1 = Encryption::hashEmail('  user@example.com  ');
        $hash2 = Encryption::hashEmail('user@example.com');

        $this->assertEquals($hash1, $hash2);
    }

    public function test_encrypt_unicode_characters(): void
    {
        $plaintext = 'María García — señor, café, niño';
        $encrypted = Encryption::encrypt($plaintext);
        $decrypted = Encryption::decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    public function test_encrypt_long_string(): void
    {
        $plaintext = str_repeat('A', 10000);
        $encrypted = Encryption::encrypt($plaintext);
        $decrypted = Encryption::decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }
}
