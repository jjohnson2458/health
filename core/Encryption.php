<?php

namespace Core;

class Encryption
{
    private const CIPHER = 'aes-256-cbc';

    private static function getKey(): string
    {
        $hex = $_ENV['ENCRYPTION_KEY'] ?? '';
        if (strlen($hex) < 64) {
            throw new \RuntimeException('ENCRYPTION_KEY must be a 64-character hex string.');
        }
        return hex2bin($hex);
    }

    public static function encrypt(?string $plaintext): ?string
    {
        if ($plaintext === null || $plaintext === '') {
            return $plaintext;
        }
        $key = self::getKey();
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));
        $ciphertext = openssl_encrypt($plaintext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            throw new \RuntimeException('Encryption failed.');
        }
        return base64_encode($iv . $ciphertext);
    }

    public static function decrypt(?string $encoded): ?string
    {
        if ($encoded === null || $encoded === '') {
            return $encoded;
        }
        $key = self::getKey();
        $data = base64_decode($encoded, true);
        if ($data === false) {
            throw new \RuntimeException('Decryption failed: invalid base64.');
        }
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($data, 0, $ivLength);
        $ciphertext = substr($data, $ivLength);
        $plaintext = openssl_decrypt($ciphertext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed.');
        }
        return $plaintext;
    }

    public static function hashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }
}
