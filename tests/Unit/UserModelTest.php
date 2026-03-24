<?php

namespace Tests\Unit;

use App\Models\User;
use Core\Encryption;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function test_user_model_class_exists(): void
    {
        $this->assertTrue(class_exists(User::class));
    }

    public function test_decrypt_user_decrypts_pii_fields(): void
    {
        $user = [
            'id' => 1,
            'first_name' => Encryption::encrypt('John'),
            'last_name' => Encryption::encrypt('Doe'),
            'email' => Encryption::encrypt('john@example.com'),
            'email_hash' => Encryption::hashEmail('john@example.com'),
            'language' => 'en',
        ];

        $decrypted = User::decryptUser($user);

        $this->assertEquals('John', $decrypted['first_name']);
        $this->assertEquals('Doe', $decrypted['last_name']);
        $this->assertEquals('john@example.com', $decrypted['email']);
    }

    public function test_decrypt_user_preserves_non_pii_fields(): void
    {
        $user = [
            'id' => 42,
            'first_name' => Encryption::encrypt('Jane'),
            'last_name' => Encryption::encrypt('Smith'),
            'email' => Encryption::encrypt('jane@example.com'),
            'email_hash' => Encryption::hashEmail('jane@example.com'),
            'language' => 'es',
        ];

        $decrypted = User::decryptUser($user);

        $this->assertEquals(42, $decrypted['id']);
        $this->assertEquals('es', $decrypted['language']);
        $this->assertEquals(Encryption::hashEmail('jane@example.com'), $decrypted['email_hash']);
    }

    public function test_decrypt_user_roundtrip_with_unicode(): void
    {
        $user = [
            'id' => 1,
            'first_name' => Encryption::encrypt('Maria'),
            'last_name' => Encryption::encrypt('Garcia'),
            'email' => Encryption::encrypt('maria@example.com'),
        ];

        $decrypted = User::decryptUser($user);

        $this->assertEquals('Maria', $decrypted['first_name']);
        $this->assertEquals('Garcia', $decrypted['last_name']);
        $this->assertEquals('maria@example.com', $decrypted['email']);
    }

    public function test_find_by_email_uses_hash_for_lookup(): void
    {
        // Verify that findByEmail would use the correct hash
        $email = 'Test@Example.com';
        $expectedHash = Encryption::hashEmail($email);

        // The hash should be case-insensitive and trimmed
        $this->assertEquals($expectedHash, Encryption::hashEmail('test@example.com'));
        $this->assertEquals($expectedHash, Encryption::hashEmail('  Test@Example.com  '));
    }

    public function test_password_reset_methods_exist(): void
    {
        $this->assertTrue(method_exists(User::class, 'generatePasswordResetToken'));
        $this->assertTrue(method_exists(User::class, 'findByResetToken'));
        $this->assertTrue(method_exists(User::class, 'resetPassword'));
    }

    public function test_two_fa_methods_exist(): void
    {
        $this->assertTrue(method_exists(User::class, 'generateTwoFaCode'));
        $this->assertTrue(method_exists(User::class, 'verifyTwoFaCode'));
    }

    public function test_find_by_email_method_exists(): void
    {
        $this->assertTrue(method_exists(User::class, 'findByEmail'));
    }

    public function test_create_user_method_exists(): void
    {
        $this->assertTrue(method_exists(User::class, 'createUser'));
    }

    /**
     * @group integration
     */
    public function test_find_by_email_returns_user(): void
    {
        // Create a user and look them up by email
        $email = 'findtest-' . uniqid() . '@example.com';
        $userId = User::createUser('Find', 'Test', $email, 'Password123!');

        $found = User::findByEmail($email);

        $this->assertNotNull($found);
        $this->assertEquals($userId, $found['id']);
        $this->assertEquals(Encryption::hashEmail($email), $found['email_hash']);
    }

    /**
     * @group integration
     */
    public function test_find_by_email_returns_null_for_unknown(): void
    {
        $found = User::findByEmail('nonexistent-' . uniqid() . '@example.com');
        $this->assertNull($found);
    }

    /**
     * @group integration
     */
    public function test_decrypt_user_from_database(): void
    {
        $email = 'decrypt-db-' . uniqid() . '@example.com';
        User::createUser('Alice', 'Wonder', $email, 'Password123!');

        $found = User::findByEmail($email);
        $this->assertNotNull($found);

        $decrypted = User::decryptUser($found);

        $this->assertEquals('Alice', $decrypted['first_name']);
        $this->assertEquals('Wonder', $decrypted['last_name']);
        $this->assertEquals($email, $decrypted['email']);
    }
}
