<?php
// Simple migration runner for local use.
// Usage: php scripts/apply_migrations.php

// Disable migrations if lock file exists
$lockFile = __DIR__ . '/../db/migrations.lock';
if (file_exists($lockFile)) {
    echo "Migrations are disabled (db/migrations.lock present). Remove the lock file to re-enable." . PHP_EOL;
    exit(0);
}

$config = include __DIR__ . '/../config/db-connect.php';
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['dbname'], $config['charset']);
try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Exception $e) {
    echo "DB connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

$dir = __DIR__ . '/../db';
$files = array_values(array_filter(scandir($dir), function($f) use ($dir){
    return is_file($dir . DIRECTORY_SEPARATOR . $f) && preg_match('/\.sql$/i', $f) && strtolower($f) !== 'brilliance-database.sql';
}));

// Sort to apply create_* before alter_* and seeds last (basic heuristic)
usort($files, function($a,$b){
    $aLower = strtolower($a); $bLower = strtolower($b);
    $score = 0;
    if (strpos($aLower,'create_') === 0) $score -= 50;
    if (strpos($bLower,'create_') === 0) $score += 50;
    if (strpos($aLower,'alter_') === 0) $score -= 10;
    if (strpos($bLower,'alter_') === 0) $score += 10;
    if (strpos($aLower,'seed_') === 0) $score += 50;
    if (strpos($bLower,'seed_') === 0) $score -= 50;
    return $score;
});

if (empty($files)) {
    echo "No SQL migration files found in db/." . PHP_EOL;
    exit(0);
}

foreach ($files as $file) {
    $path = $dir . DIRECTORY_SEPARATOR . $file;
    echo "Applying $file...\n";
    $sql = file_get_contents($path);
    if ($sql === false) {
        echo "Unable to read $file\n";
        continue;
    }
    try {
        // Split statements by semicolon on line-ends — crude but works for simple migrations
        $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
        foreach ($stmts as $stmt) {
            if ($stmt === '') continue;
            try {
                $pdo->exec($stmt);
            } catch (PDOException $pe) {
                $msg = $pe->getMessage();
                // If column already exists or duplicate key/constraint, skip and continue
                if (stripos($msg, 'Duplicate column name') !== false || stripos($msg, 'already exists') !== false || stripos($msg, 'Duplicate key name') !== false) {
                    echo "Warning (non-fatal): " . $msg . "\n";
                    continue;
                }
                throw $pe;
            }
        }
        echo "Applied: $file\n";
    } catch (Exception $e) {
        echo "Failed to apply $file: " . $e->getMessage() . PHP_EOL;
        echo "Stopping further migrations." . PHP_EOL;
        exit(1);
    }
}

echo "All migrations applied (or skipped BRILLIANCE-DATABASE.SQL).\n";

?>