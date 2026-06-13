<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Brilliance</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/brilliance/assets/css/auth.css">

</head>

<body>

<section class="auth-section">
<div class="auth-container">

<a href="../index.php" class="back-home">← Back Home</a>

<div class="auth-header">
<h1 class="auth-title">Welcome</h1>
<p class="auth-subtitle">Login to your account</p>
</div>

<?php if(isset($_SESSION['success'])): ?>
    <div class="success-msg"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="error-msg"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="auth-card">

<form method="POST" action="../processes/login-processes.php">

  <?php echo csrf_field(); ?>

<div class="input-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="input-group">
<label>Password</label>
<input type="password" name="password" required>
</div>

<button class="submit-btn" name="login">Login</button>

</form>

<!-- ✅ ADDED FOOTER -->
<div class="auth-footer">
  Don’t have an account?
  <a href="register.php">Create one here</a>
</div>

</div>

</div>
</section>

</body>
</html>