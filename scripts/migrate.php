<?php
/**
 * Database Migration Runner
 * Scans database/migrations/ for SQL files and executes unrun migrations.
 * Tracks executed migrations in a `migrations` table.
 */

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($projectRoot);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$name = $_ENV['DB_DATABASE'] ?? 'claude_health';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Create migrations tracking table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL UNIQUE,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Get already-run migrations
$executed = $pdo->query("SELECT filename FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

// Scan migration files
$migrationsDir = $projectRoot . '/database/migrations';
if (!is_dir($migrationsDir)) {
    echo "No migrations directory found.\n";
    exit(0);
}

$files = glob($migrationsDir . '/*.sql');
sort($files);

$ran = 0;
foreach ($files as $file) {
    $filename = basename($file);

    if (in_array($filename, $executed, true)) {
        continue;
    }

    echo "Running: {$filename}... ";

    $sql = file_get_contents($file);
    if (empty(trim($sql))) {
        echo "EMPTY (skipped)\n";
        continue;
    }

    try {
        $pdo->exec($sql);
        $stmt = $pdo->prepare("INSERT INTO migrations (filename) VALUES (?)");
        $stmt->execute([$filename]);
        echo "OK\n";
        $ran++;
    } catch (PDOException $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        exit(1);
    }
}

if ($ran === 0) {
    echo "Nothing to migrate.\n";
} else {
    echo "Ran {$ran} migration(s) successfully.\n";
}
