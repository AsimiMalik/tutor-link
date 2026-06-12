<?php
require_once __DIR__ . '/../processes/parent-edit-profile-processes.php';
require_once __DIR__ . '/../includes/csrf.php';
?>
// intentionally do not include the navbar on edit profile pages

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>

    <link rel="stylesheet" href="/tutorlink/assets/css/tutor-edit-profile.css">
</head>

<body>

<?php /* navbar intentionally omitted on edit profile */ ?>

<section class="dashboard-section">
<div class="container" style="margin-top:90px;">

<div class="section-title">
    <h2>Edit Profile</h2>
</div>

<form class="tutor-card"
      method="POST"
      action="../processes/parent-update-profile-processes.php"
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

    <!-- LOCATION -->
    <label>Location</label>
    <input type="text" name="location" value="<?php echo $data['location']; ?>">

    <button class="btn-primary" name="update">
        Save Changes
    </button>

</form>

</div>
</section>

<script>
// image preview for parent edit profile
document.addEventListener('DOMContentLoaded', function(){
    const fileInput = document.querySelector('input[type=file][name=profile_pic]');
    if (!fileInput) return;
    const preview = document.querySelector('.avatar-wrapper');

    fileInput.addEventListener('change', function(e){
        const f = e.target.files[0];
        if (!f) return;
        const reader = new FileReader();
        reader.onload = function(ev){
            // replace or insert img.preview
            let img = preview.querySelector('img.preview');
            if (!img) {
                img = document.createElement('img');
                img.className = 'profile-avatar preview';
                preview.innerHTML = '';
                preview.appendChild(img);
            }
            img.src = ev.target.result;
        };
        reader.readAsDataURL(f);
    });
});
</script>

</body>
</html>
