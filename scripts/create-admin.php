<?php
/**
 * Create Admin Account
 * Usage: php scripts/create-admin.php
 */

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($projectRoot);
$dotenv->load();

require_once $projectRoot . '/app/Helpers/functions.php';

use Core\Encryption;
use Core\Database;

$email = 'admin';
$password = 'Admin123!';
$firstName = 'Admin';
$lastName = 'User';

$db = Database::getInstance();

// Check if admin already exists
$emailHash = Encryption::hashEmail($email);
$stmt = $db->prepare('SELECT id FROM users WHERE email_hash = ?');
$stmt->execute([$emailHash]);
if ($stmt->fetch()) {
    // Update existing to admin role
    $stmt = $db->prepare('UPDATE users SET role = ? WHERE email_hash = ?');
    $stmt->execute(['admin', $emailHash]);
    echo "Existing admin account updated to admin role.\n";
    exit(0);
}

// Create new admin
$stmt = $db->prepare(
    'INSERT INTO users (first_name, last_name, email, email_hash, password_hash, email_verified, role, language)
     VALUES (?, ?, ?, ?, ?, 1, ?, ?)'
);
$stmt->execute([
    Encryption::encrypt($firstName),
    Encryption::encrypt($lastName),
    Encryption::encrypt($email),
    $emailHash,
    password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
    'admin',
    'en',
]);

echo "Admin account created: {$email} / Admin123!\n";
echo "ID: " . $db->lastInsertId() . "\n";
