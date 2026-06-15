<?php
// Temporary endpoint to run migrations via web PHP. Remove after use.
$TOKEN = 'run-migs-9f3c2a7';
if (!isset($_GET['token']) || $_GET['token'] !== $TOKEN) {
    http_response_code(403);
    echo "Access denied.";
    exit();
}

// Buffer output
ob_start();
require_once __DIR__ . '/apply_migrations.php';
$out = ob_get_clean();

// Also run reconcile script if present
ob_start();
if (is_file(__DIR__ . '/reconcile_qualification_files.php')) {
    require_once __DIR__ . '/reconcile_qualification_files.php';
}
$recon = ob_get_clean();

echo "MIGRATIONS OUTPUT:\n";
echo $out;

echo "\nRECONCILE OUTPUT:\n";
echo $recon;

?>