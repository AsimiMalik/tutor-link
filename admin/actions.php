<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();
require_once __DIR__ . '/../includes/csrf.php';

// sanitize
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : (isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0);

// Toggle suspend / active
if (isset($_POST['suspend'])) {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['error']='Invalid CSRF token'; header('Location: users.php'); exit(); }
    try {
        // Ensure is_active exists before querying/updating
        $colCheck = $conn->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = ? AND table_name = 'users' AND column_name = 'is_active'");
        $cfg = include __DIR__ . '/../config/db-connect.php';
        $colCheck->execute([$cfg['dbname']]);
        $hasIsActive = (bool)$colCheck->fetchColumn();
        if (!$hasIsActive) {
            $_SESSION['error'] = 'Database missing `users.is_active` column. Run migrations to add it.';
            header('Location: users.php'); exit();
        }

        $stmt = $conn->prepare('SELECT is_active FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$user_id]); $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $new = ($row && ($row['is_active'] ?? 1)) ? 0 : 1;
        $u = $conn->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $u->execute([$new,$user_id]);
        $_SESSION['success'] = 'User active toggled.';
        try {
            $log = $conn->prepare('INSERT INTO admin_audit (admin_user_id, action, target_user_id, details) VALUES (?, ?, ?, ?)');
            $log->execute([$_SESSION['user_id'], 'toggle_active', $user_id, json_encode(['new' => $new])]);
        } catch (Exception $e) { /* ignore if audit table missing */ }
    } catch (Exception $e) { $_SESSION['error'] = 'Unable to toggle active.'; }
    header('Location: users.php'); exit();
}

// Make admin
if (isset($_POST['make_admin'])) {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['error']='Invalid CSRF token'; header('Location: users.php'); exit(); }
    try {
        $r = $conn->prepare('UPDATE users SET role = ? WHERE id = ?');
        $r->execute(['admin',$user_id]);
        $_SESSION['success'] = 'User promoted to admin.';
        try {
            $log = $conn->prepare('INSERT INTO admin_audit (admin_user_id, action, target_user_id, details) VALUES (?, ?, ?, ?)');
            $log->execute([$_SESSION['user_id'], 'promote_admin', $user_id, null]);
        } catch (Exception $e) { }
    } catch (Exception $e) { $_SESSION['error']='Unable to promote user.'; }
    header('Location: users.php'); exit();
}

// Change role
if (isset($_POST['change_role'])) {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['error']='Invalid CSRF token'; header('Location: users.php'); exit(); }
    $newRole = trim($_POST['new_role'] ?? '');
    if (!in_array($newRole, ['parent','tutor','admin'])) { $_SESSION['error']='Invalid role.'; header('Location: users.php'); exit(); }
    try {
        $r = $conn->prepare('UPDATE users SET role = ? WHERE id = ?');
        $r->execute([$newRole,$user_id]);
        $_SESSION['success'] = 'User role updated.';
        try {
            $log = $conn->prepare('INSERT INTO admin_audit (admin_user_id, action, target_user_id, details) VALUES (?, ?, ?, ?)');
            $log->execute([$_SESSION['user_id'], 'change_role', $user_id, json_encode(['role'=>$newRole])]);
        } catch (Exception $e) { }
    } catch (Exception $e) { $_SESSION['error']='Unable to change role.'; }
    header('Location: users.php'); exit();
}

// Delete user
if (isset($_POST['delete_user'])) {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['error']='Invalid CSRF token'; header('Location: users.php'); exit(); }
    try {
        // delete cascade: remove related rows (best-effort). Keep it simple: delete user row only.
        $d = $conn->prepare('DELETE FROM users WHERE id = ?');
        $d->execute([$user_id]);
        $_SESSION['success'] = 'User deleted.';
        try {
            $log = $conn->prepare('INSERT INTO admin_audit (admin_user_id, action, target_user_id, details) VALUES (?, ?, ?, ?)');
            $log->execute([$_SESSION['user_id'], 'delete_user', $user_id, null]);
        } catch (Exception $e) { }
    } catch (Exception $e) { $_SESSION['error']='Unable to delete user.'; }
    header('Location: users.php'); exit();
}

// Verify tutor
if (isset($_POST['verify_tutor'])) {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['error']='Invalid CSRF token'; header('Location: tutors.php'); exit(); }
    try {
        $v = $conn->prepare('UPDATE tutor_profile SET is_verified = 1 WHERE user_id = ?');
        $v->execute([$user_id]);
        $_SESSION['success'] = 'Tutor verified.';
        try {
            $log = $conn->prepare('INSERT INTO admin_audit (admin_user_id, action, target_user_id, details) VALUES (?, ?, ?, ?)');
            $log->execute([$_SESSION['user_id'], 'verify_tutor', $user_id, null]);
        } catch (Exception $e) { }
    } catch (Exception $e) { $_SESSION['error'] = 'Unable to verify tutor.'; }
    header('Location: tutors.php'); exit();
}

// export admin audit logs as CSV if requested
if (isset($_GET['action']) && $_GET['action'] === 'export_logs') {
    try {
        $stmt = $conn->query('SELECT id, admin_user_id, action, target_user_id, details, created_at FROM admin_audit ORDER BY created_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="admin_audit.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id','admin_user_id','action','target_user_id','details','created_at']);
        foreach ($rows as $r) fputcsv($out, [$r['id'],$r['admin_user_id'],$r['action'],$r['target_user_id'],$r['details'],$r['created_at']]);
        fclose($out);
    } catch (Exception $e) {
        header('Content-Type: text/plain');
        echo "No audit logs available or error: " . $e->getMessage();
    }
    exit();
}

header('Location: dashboard.php'); exit();
