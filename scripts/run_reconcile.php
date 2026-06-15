<?php
session_start();
// Web-accessible reconciliation tool. Requires logged-in admin role.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied. Admins only.";
    exit();
}

// Run reconcile logic and display results
$config = include __DIR__ . '/../config/db-connect.php';
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['dbname'], $config['charset']);
try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Exception $e) {
    echo "DB connection failed: " . htmlspecialchars($e->getMessage());
    exit();
}

$dir = __DIR__ . '/../uploads/qualifications';
if (!is_dir($dir)) {
    echo "Uploads qualifications directory not found: " . htmlspecialchars($dir);
    exit();
}

$files = scandir($dir);
$updated = 0;
$skipped = 0;
$errors = [];
$output = [];
foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    if (!is_file($dir . '/' . $f)) continue;
    if (preg_match('/^(\d+)_/', $f, $m)) {
        $userId = (int)$m[1];
        try {
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
            $output[] = "Linked user $userId -> $f";
        } catch (Exception $e) {
            $errors[] = "Error for $f: " . $e->getMessage();
        }
    } else {
        $skipped++;
    }
}

?><!doctype html>
<html><head><meta charset="utf-8"><title>Reconcile Qualifications</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:900px;margin:130px auto;padding:20px">
    <h2>Reconcile Qualification Files</h2>
    <p>Action run by admin: <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
    <div class="glass-card" style="padding:12px;margin-top:12px">
        <pre><?php
            echo "Updated: $updated\n";
            echo "Skipped (no prefix): $skipped\n\n";
            if (!empty($output)) echo implode("\n", $output) . "\n";
            if (!empty($errors)) echo "Errors:\n" . implode("\n", $errors) . "\n";
        ?></pre>
    </div>
</div>
</body></html>
