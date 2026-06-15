<?php
// mysqli-based migration runner for environments where pdo_mysql isn't available
// Usage: php scripts/apply_migrations_mysqli.php
$config = include __DIR__ . '/../config/db-connect.php';
$host = $config['host'];
$user = $config['username'];
$pass = $config['password'];
$dbname = $config['dbname'];
$charset = isset($config['charset']) ? $config['charset'] : 'utf8mb4';

$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
if (!$mysqli->real_connect($host, $user, $pass, $dbname)) {
    echo "DB connection failed: " . mysqli_connect_error() . PHP_EOL;
    exit(1);
}
$dir = __DIR__ . '/../db';
$files = array_values(array_filter(scandir($dir), function($f) use ($dir){
    return is_file($dir . DIRECTORY_SEPARATOR . $f) && preg_match('/\.sql$/i', $f) && strtolower($f) !== 'brilliance-database.sql';
}));

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
    // Split by semicolon followed by newline(s). Keep simple.
    $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
    foreach ($stmts as $stmt) {
        if ($stmt === '') continue;
        if (!$mysqli->multi_query($stmt)) {
            echo "Failed to apply $file: " . $mysqli->error . PHP_EOL;
            // drain
            while ($mysqli->more_results() && $mysqli->next_result()) { /* noop */ }
            $mysqli->close();
            exit(1);
        }
        // consume possible results
        do { if ($res = $mysqli->store_result()) { $res->free(); } } while ($mysqli->more_results() && $mysqli->next_result());
    }
    echo "Applied: $file\n";
}

$mysqli->close();
echo "All migrations applied (or skipped BRILLIANCE-DATABASE.SQL).\n";

?>