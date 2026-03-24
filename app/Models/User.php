<?php

namespace App\Models;

use Core\Database;
use Core\Encryption;
use Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        $hash = Encryption::hashEmail($email);
        return static::whereFirst('email_hash', $hash);
    }

    public static function createUser(string $firstName, string $lastName, string $email, string $password): int
    {
        return static::create([
            'first_name' => Encryption::encrypt($firstName),
            'last_name' => Encryption::encrypt($lastName),
            'email' => Encryption::encrypt($email),
            'email_hash' => Encryption::hashEmail($email),
            'password_hash' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'email_token' => bin2hex(random_bytes(32)),
            'email_token_expires' => date('Y-m-d H:i:s', strtotime('+24 hours')),
        ]);
    }

    public static function decryptUser(array $user): array
    {
        $user['first_name'] = Encryption::decrypt($user['first_name']);
        $user['last_name'] = Encryption::decrypt($user['last_name']);
        $user['email'] = Encryption::decrypt($user['email']);
        return $user;
    }

    public static function generateTwoFaCode(int $userId): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        static::update($userId, [
            'twofa_code' => $code,
            'twofa_expires' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
        ]);
        return $code;
    }

    public static function verifyTwoFaCode(int $userId, string $code): bool
    {
        $user = static::find($userId);
        if (!$user) {
            return false;
        }
        if ($user['twofa_code'] !== $code) {
            return false;
        }
        if (strtotime($user['twofa_expires']) < time()) {
            return false;
        }
        // Clear code after use
        static::update($userId, [
            'twofa_code' => null,
            'twofa_expires' => null,
        ]);
        return true;
    }

    public static function generatePasswordResetToken(string $email): ?string
    {
        $user = static::findByEmail($email);
        if (!$user) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        static::update($user['id'], [
            'password_reset_token' => $token,
            'password_reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ]);

        return $token;
    }

    public static function findByResetToken(string $token): ?array
    {
        $user = static::whereFirst('password_reset_token', $token);
        if (!$user) {
            return null;
        }
        if (strtotime($user['password_reset_expires']) < time()) {
            return null;
        }
        return $user;
    }

    public static function resetPassword(int $userId, string $newPassword): void
    {
        static::update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
            'password_reset_token' => null,
            'password_reset_expires' => null,
        ]);
    }
}
