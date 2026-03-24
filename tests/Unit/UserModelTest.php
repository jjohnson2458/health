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
}
