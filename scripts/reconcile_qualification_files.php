<?php
// Scans uploads/qualifications for files named like '<userId>_...'
// and updates tutor_profile.qualification_file for that user.
// Usage: php scripts/reconcile_qualification_files.php

require_once __DIR__ . '/../config/db-connect.php';
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

$dir = __DIR__ . '/../uploads/qualifications';
if (!is_dir($dir)) {
    echo "Qualification uploads directory not found: $dir\n";
    exit(1);
}

$files = scandir($dir);
$updated = 0;
$skipped = 0;
$errors = [];
foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    if (!is_file($dir . '/' . $f)) continue;
    if (preg_match('/^(\d+)_/', $f, $m)) {
        $userId = (int)$m[1];
        try {
            // ensure tutor_profile exists
            $stmt = $pdo->prepare('SELECT id FROM tutor_profile WHERE user_id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($exists) {
                $up = $pdo->prepare('UPDATE tutor_profile SET qualification_file = ?, updated_at = NOW() WHERE user_id = ?');
                $up->execute([$f, $userId]);
            } else {
                $ins = $pdo->prepare('INSERT INTO tutor_profile (user_id, qualification_file, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
                $ins->execute([$userId, $f]);
            }
            $updated++;
            echo "Updated user $userId => $f\n";
        } catch (Exception $e) {
            $errors[] = "Error for $f: " . $e->getMessage();
        }
    } else {
        $skipped++;
    }
}

echo "\nSummary: $updated files linked, $skipped files skipped (no user prefix)." . PHP_EOL;
if (!empty($errors)) {
    echo "Errors:\n" . implode("\n", $errors) . PHP_EOL;
}

?>