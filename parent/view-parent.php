<?php
session_start();
require_once __DIR__ . '/../classes/database.php';

$db = new Database();
$conn = $db->connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    echo 'Parent not found';
    exit();
}

$stmt = $conn->prepare("SELECT u.fullname, u.email, p.* FROM users u LEFT JOIN parent_profile p ON u.id = p.user_id WHERE u.id = ? AND u.role = 'parent'");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    http_response_code(404);
    echo 'Parent not found';
    exit();
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars($data['fullname']); ?> — Parent | Brilliance</title>
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <link rel="stylesheet" href="/brilliance/assets/css/tutor-profile.css">
    <style>.profile-wrapper{margin-top:90px}</style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="profile-wrapper">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-img">
                <?php if(!empty($data['profile_pic'])): ?>
                    <img src="/brilliance/uploads/<?php echo htmlspecialchars($data['profile_pic']); ?>" alt="Profile" class="profile-avatar-large">
                <?php else: ?>
                    <div class="no-img">No Image</div>
                <?php endif; ?>
            </div>

            <div class="profile-info">
                <h2><?php echo htmlspecialchars($data['fullname'] ?? 'Parent'); ?></h2>
                <p class="location">📍 <?php echo htmlspecialchars($data['location'] ?? 'No location set'); ?></p>
                <p class="rate muted"><?= htmlspecialchars($data['email'] ?? '') ?></p>
            </div>

        </div>

        <div class="profile-body">
            <div class="section">
                <h3>Bio</h3>
                <p><?php echo nl2br(htmlspecialchars($data['bio'] ?? 'No bio provided')); ?></p>
            </div>

        </div>
    </div>
</div>

</body>
</html>
