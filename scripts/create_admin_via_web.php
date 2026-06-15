<?php
// Web helper to create or promote an admin user.
// Usage: visit in browser: /brilliance/scripts/create_admin_via_web.php?token=9f3c2a7b8d6e4f12b0c3d9e8a1f6b2c
// The page shows a form to enter email and password and will create or promote the user to role=admin.

// NOTE: Remove this file after use.

$EXPECTED_TOKEN = '9f3c2a7b8d6e4f12b0c3d9e8a1f6b2c';
session_start();
if (!isset($_GET['token']) || $_GET['token'] !== $EXPECTED_TOKEN) {
    http_response_code(403);
    echo "Access denied. Provide valid token.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $config = include __DIR__ . '/../config/db-connect.php';
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['dbname'], $config['charset']);
        try {
            $pdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
        } catch (Exception $e) {
            $error = 'DB connection failed: ' . $e->getMessage();
        }
        if (empty($error)) {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                // check if user exists
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $pdo->prepare('UPDATE users SET password = ?, role = ? WHERE id = ?')->execute([$hash, 'admin', $row['id']]);
                    $msg = 'Existing user promoted to admin and password updated.';
                } else {
                    $pdo->prepare('INSERT INTO users (fullname, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())')
                        ->execute(['Admin User', $email, $hash, 'admin']);
                    $msg = 'New admin user created.';
                }
            } catch (Exception $e) {
                $error = 'DB error: ' . $e->getMessage();
            }
        }
    }
}

?><!doctype html>
<html><head><meta charset="utf-8"><title>Create Admin</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:700px;margin:120px auto;padding:20px">
  <h2>Create / Promote Admin</h2>
  <p>This helper creates or promotes a user to admin. Remove this file after use.</p>
  <?php if (!empty($error)): ?><div class="glass-card" style="color:#900;margin-bottom:12px"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <?php if (!empty($msg)): ?><div class="glass-card" style="color:#080;margin-bottom:12px"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
  <div class="glass-card" style="padding:12px">
    <form method="post">
      <label>Email<br><input type="email" name="email" required style="width:100%"></label><br><br>
      <label>Password<br><input type="password" name="password" required style="width:100%"></label><br><br>
      <button class="btn-primary" type="submit">Create / Promote Admin</button>
    </form>
  </div>
</div>
</body></html>
