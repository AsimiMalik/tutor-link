<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | TutorLink</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
<?php include '../assets/css/auth.css'; ?>
</style>

</head>

<body>

<section class="auth-section">
<div class="auth-container">

<a href="../index.php" class="back-home">← Back Home</a>

<div class="auth-header">
<h1 class="auth-title">Create Account</h1>
<p class="auth-subtitle">Join TutorLink as a Tutor or Parent</p>
</div>

<?php if(isset($_SESSION['success'])): ?>
    <div class="success-msg"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="error-msg"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="role-grid">

<div class="role-card active" onclick="showForm('tutor',this)">
<h3>Tutor</h3>
</div>

<div class="role-card" onclick="showForm('parent',this)">
<h3>Parent</h3>
</div>

</div>

<form id="tutorForm" class="auth-card" method="POST" action="../processes/register-processes.php">

<input type="hidden" name="role" value="tutor">

<div class="form-grid">

<div class="input-group">
<label>Full Name</label>
<input type="text" name="fullname" required>
</div>

<div class="input-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="input-group">
<label>Password</label>
<input type="password" name="password" required>
</div>

<div class="input-group">
<label>Confirm Password</label>
<input type="password" name="confirm_password" required>
</div>

</div>

<button class="submit-btn" name="register">Create Tutor Account</button>

</form>

<form id="parentForm" class="auth-card hidden" method="POST" action="../processes/register-processes.php">

<input type="hidden" name="role" value="parent">

<div class="form-grid">

<div class="input-group">
<label>Full Name</label>
<input type="text" name="fullname" required>
</div>

<div class="input-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="input-group">
<label>Password</label>
<input type="password" name="password" required>
</div>
<div class="input-group">
<label>Confirm Password</label>
<input type="password" name="confirm_password" required>
</div>
</div>

<button class="submit-btn" name="register">Create Parent Account</button>

</form>

<!-- ✅ ADDED PART -->
<div class="auth-footer">
  Already have an account?
  <a href="login.php">Login here</a>
</div>

</div>
</section>

<script>
function showForm(role, card){
document.getElementById("tutorForm").classList.add("hidden");
document.getElementById("parentForm").classList.add("hidden");

document.getElementById(role + "Form").classList.remove("hidden");

document.querySelectorAll(".role-card").forEach(c=>{
c.classList.remove("active");
});

card.classList.add("active");
}
</script>

</body>
</html>