<?php
/*
----------------------------------------------------
LOAD PROFILE DATA FIRST
This creates $data variable from database
----------------------------------------------------
*/
 require_once __DIR__ . '/../processes/tutor-profile-processes.php'; 
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
    <title>My Tutor Profile</title>

     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <link rel="stylesheet" href="/brilliance/assets/css/tutor-profile.css">
</head>

<body>

<?php include '../includes/tutor-navbar.php'; ?>

<div class="profile-wrapper">

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
                    <?php echo htmlspecialchars($data['fullname'] ?? 'Tutor'); ?>

                    <?php if(!empty($data['is_verified'])): ?>
                        <span class="badge">Verified</span>
                    <?php endif; ?>
                </h2>
                <p class="location">
                    📍 <?php echo htmlspecialchars($data['location'] ?? 'No location set'); ?>
                </p>

                <p class="rating">
                    ⭐ <?php echo $data['rating_avg'] ?? '0'; ?> 
                    (<?php echo $data['total_reviews'] ?? '0'; ?> reviews)
                </p>

                <p class="rate">
                    ₦<?php echo $data['hourly_rate'] ?? '0'; ?> / hour
                </p>
            </div>

            <div class="stats-row">
                <div class="stat">
                    <h4><?php echo $data['rating_avg'] ?? '0'; ?></h4>
                    <p>Average Rating</p>
                </div>

                <div class="stat">
                    <h4><?php echo $data['total_reviews'] ?? '0'; ?></h4>
                    <p>Reviews</p>
                </div>

                <div class="stat">
                    <h4>—</h4>
                    <p>Subjects</p>
                </div>
            </div>

        </div>

        <div class="profile-body">

            <div class="section">
                <h3>Bio</h3>
                <p><?php echo $data['bio'] ?? 'No bio yet'; ?></p>
            </div>

            <div class="section">
                <h3>Experience</h3>
                <p><?php echo $data['experience'] ?? 'No experience added'; ?></p>
            </div>

            <div class="section">
                <h3>Subjects</h3>
                <p>
                    <?php if (!empty($data['subjects'])): ?>
                        <?= htmlspecialchars(implode(', ', $data['subjects'])) ?>
                    <?php else: ?>
                        No subjects added
                    <?php endif; ?>
                </p>
            </div>

            <div class="section">
                <h3>Qualifications</h3>
                <p><?php echo nl2br(htmlspecialchars($data['qualification'] ?? 'No qualifications added')); ?></p>
            </div>

            <a href="tutor-edit-profile.php" class="btn">Edit Profile</a>

        </div>

    </div>

</div>

</body>
</html>