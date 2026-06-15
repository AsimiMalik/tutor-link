<?php
// Usage: php scripts/check_tutor_qualification.php <tutor_id>
if ($argc < 2) {
    echo "Usage: php check_tutor_qualification.php <tutor_id>\n";
    exit(1);
}
$tutorId = (int)$argv[1];
if ($tutorId <= 0) { echo "Invalid tutor id\n"; exit(1); }
$config = include __DIR__ . '/../config/db-connect.php';
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['dbname'], $config['charset']);
try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "DB connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
$stmt = $pdo->prepare('SELECT * FROM tutor_profile WHERE user_id = ? LIMIT 1');
$stmt->execute([$tutorId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo "No tutor_profile row found for user_id={$tutorId}\n";
} else {
    echo "tutor_profile for user_id={$tutorId}:\n";
    echo "  qualification_file: " . ($row['qualification_file'] ?? '(null)') . "\n";
    echo "  profile_pic: " . ($row['profile_pic'] ?? '(null)') . "\n";
}
$dir = __DIR__ . '/../uploads/qualifications';
if (!is_dir($dir)) {
    echo "Uploads qualifications directory does not exist: $dir\n";
    exit(0);
}
$files = scandir($dir);
$found = false;
foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    if (preg_match('/^' . preg_quote((string)$tutorId, '/') . '_/', $f)) {
        echo "Found file with tutor prefix: $f\n";
        $found = true;
    }
}
if (!$found) echo "No files in uploads/qualifications/ with tutor prefix '{$tutorId}_' found.\n";
// also check for any file in folder matching qualification_file name
if (!empty($row['qualification_file'])) {
    $path = __DIR__ . '/../uploads/qualifications/' . $row['qualification_file'];
    echo "Checking file path: $path\n";
    echo file_exists($path) ? "File exists on disk.\n" : "File NOT found on disk.\n";
}
?>