<?php
// load parent profile data
 require_once __DIR__ . '/../processes/parent-profile-processes.php';
?>

<?php if(isset($_SESSION['success'])): ?>
    <div class="success-msg"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <div class="error-msg"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <link rel="stylesheet" href="/brilliance/assets/css/tutor-profile.css">
</head>

<body>

<?php include __DIR__ . '/../includes/parent-navbar.php'; ?>

<div class="profile-wrapper" style="margin-top:90px;">

    <div class="profile-card">

        <div class="profile-header">

            <div class="profile-img">
                <?php if(!empty($data['profile_pic'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($data['profile_pic']); ?>" alt="Profile" class="profile-avatar-large">
                <?php else: ?>
                    <div class="no-img">No Image</div>
                <?php endif; ?>
            </div>

            <div class="profile-info">
                <h2>
                    <?php echo htmlspecialchars($data['fullname'] ?? 'Parent'); ?>
                </h2>
                <p class="location">
                    📍 <?php echo htmlspecialchars($data['location'] ?? 'No location set'); ?>
                </p>

                <p class="rate muted">
                    <?= htmlspecialchars($data['email'] ?? '') ?>
                </p>
            </div>

        </div>

        <div class="profile-body">

            <div class="section">
                <h3>Bio</h3>
                <p><?php echo $data['bio'] ?? 'No bio yet'; ?></p>
            </div>

            <a href="parent-edit-profile.php" class="btn">Edit Profile</a>

        </div>

    </div>

</div>

</body>
</html>
