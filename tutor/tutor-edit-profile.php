<?php
/*
----------------------------------------------------
LOAD LOGIC FILE FIRST
----------------------------------------------------
*/
require_once "../processes/tutor-edit-profile-processes.php";
require_once __DIR__ . '/../includes/csrf.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>

    <link rel="stylesheet" href="/brilliance/assets/css/tutor-edit-profile.css">
</head>

<body>

<!-- <?php include '../includes/tutor-navbar.php'; ?> -->

<section class="dashboard-section">
<div class="container">

<div class="section-title">
    <h2>Edit Profile</h2>
</div>

<form class="tutor-card"
      method="POST"
      action="../processes/tutor-update-profile-processes.php"
      enctype="multipart/form-data">

    <!-- ID -->
    <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

    <!-- AVATAR PREVIEW -->
    <div class="avatar-wrapper">
        <?php if(!empty($data['profile_pic'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($data['profile_pic']); ?>" alt="Profile" class="profile-avatar">
        <?php else: ?>
            <div class="no-img">No Image</div>
        <?php endif; ?>
    </div>

    <!-- PROFILE PIC -->
    <label>Profile Picture</label>
    <input type="file" name="profile_pic">

    <?php echo csrf_field(); ?>

    <!-- FULL NAME (READ ONLY) -->
    <label>Full Name</label>
    <input type="text" value="<?php echo htmlspecialchars($data['fullname']); ?>" disabled>

    <!-- EMAIL (READ ONLY) -->
    <label>Email</label>
    <input type="text" value="<?php echo htmlspecialchars($data['email']); ?>" disabled>

    <!-- BIO -->
    <label>Bio</label>
    <textarea name="bio"><?php echo $data['bio']; ?></textarea>

    <!-- QUALIFICATION -->
    <label>Qualifications</label>
    <textarea name="qualification" placeholder="List degrees, certifications, institutions, years etc."><?php echo htmlspecialchars($data['qualification'] ?? ''); ?></textarea>

    <!-- EXPERIENCE -->
    <label>Experience</label>
    <textarea name="experience"><?php echo $data['experience']; ?></textarea>

    <!-- LOCATION -->
    <label>Location</label>
    <input type="text" name="location" value="<?php echo $data['location']; ?>">

    <!-- HOURLY RATE -->
    <label>Hourly Rate</label>
    <input type="number" name="hourly_rate" value="<?php echo $data['hourly_rate']; ?>">

    <!-- SUBJECTS -->
    <label>Subjects</label>
    <div style="margin-bottom:12px">
        <?php if (!empty($subjects)): ?>
            <?php foreach ($subjects as $s): ?>
                <label style="display:inline-block;margin-right:8px">
                    <input type="checkbox" name="subjects[]" value="<?= $s['id'] ?>" <?php if (!empty($assigned_subjects) && in_array($s['id'], $assigned_subjects)) echo 'checked'; ?>>
                    <?= htmlspecialchars($s['name']) ?>
                </label>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="muted">No subjects available. Ask admin to add subjects.</div>
        <?php endif; ?>
    </div>

    <button class="btn-primary" name="update">
        Save Changes
    </button>

</form>

</div>
</section>

</body>
</html>