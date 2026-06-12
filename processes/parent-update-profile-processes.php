<?php
session_start();

// simple error reporting for dev
ini_set('display_errors',1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../classes/Database.php';
$db = new Database();
$conn = $db->connect();

require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        header('Location: ../parent/parent-edit-profile.php');
        exit();
    }
}

$user_id = $_SESSION['user_id'];

$bio = $_POST['bio'] ?? '';
$location = $_POST['location'] ?? '';

$profile_pic_name = null;
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $file = $_FILES['profile_pic'];
    $file_name = time() . '_' . basename($file['name']);
    $target_dir = __DIR__ . '/../uploads/';
    if (!is_dir($target_dir)) mkdir($target_dir,0755,true);
    $target_file = $target_dir . $file_name;
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        $profile_pic_name = $file_name;
    }
}

// check if parent_profile exists
$stmt = $conn->prepare('SELECT id FROM parent_profile WHERE user_id = ?');
$stmt->execute([$user_id]);
$exists = $stmt->fetch();

// attempt to repair incorrect foreign keys on parent_profile (if present)
function repair_parent_profile_fk($conn) {
    try {
        $schema = $conn->query('SELECT DATABASE()')->fetchColumn();
        $q = "SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE
              WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'parent_profile' AND REFERENCED_TABLE_NAME IS NOT NULL";
        $s = $conn->prepare($q);
        $s->execute([$schema]);
        $fks = $s->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fks as $fk) {
            if ($fk['REFERENCED_TABLE_NAME'] !== 'users') {
                $cname = $fk['CONSTRAINT_NAME'];
                $conn->exec("ALTER TABLE parent_profile DROP FOREIGN KEY `" . $cname . "`");
            }
        }
        // ensure correct FK exists
        try { $conn->exec("ALTER TABLE parent_profile DROP FOREIGN KEY fk_parent_profile_user"); } catch (Exception $e) {}
        $conn->exec("ALTER TABLE parent_profile ADD CONSTRAINT fk_parent_profile_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($exists) {
    if ($profile_pic_name) {
        $upd = $conn->prepare('UPDATE parent_profile SET bio = ?, location = ?, profile_pic = ? WHERE user_id = ?');
        try {
            $upd->execute([$bio, $location, $profile_pic_name, $user_id]);
        } catch (PDOException $e) {
            // attempt FK repair if this looks like a foreign-key issue
            if (stripos($e->getMessage(), 'foreign key') !== false || stripos($e->getMessage(), '1452') !== false) {
                if (repair_parent_profile_fk($conn)) {
                    try {
                        $upd->execute([$bio, $location, $profile_pic_name, $user_id]);
                    } catch (PDOException $e2) {
                        $_SESSION['error'] = 'Database error updating profile after repair: ' . $e2->getMessage();
                        header('Location: ../parent/parent-edit-profile.php');
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Database FK error updating profile: ' . $e->getMessage();
                    header('Location: ../parent/parent-edit-profile.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Database error updating profile: ' . $e->getMessage();
                header('Location: ../parent/parent-edit-profile.php');
                exit();
            }
        }
    } else {
        $upd = $conn->prepare('UPDATE parent_profile SET bio = ?, location = ? WHERE user_id = ?');
        try {
            $upd->execute([$bio, $location, $user_id]);
        } catch (PDOException $e) {
            if (stripos($e->getMessage(), 'foreign key') !== false || stripos($e->getMessage(), '1452') !== false) {
                if (repair_parent_profile_fk($conn)) {
                    try {
                        $upd->execute([$bio, $location, $user_id]);
                    } catch (PDOException $e2) {
                        $_SESSION['error'] = 'Database error updating profile after repair: ' . $e2->getMessage();
                        header('Location: ../parent/parent-edit-profile.php');
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Database FK error updating profile: ' . $e->getMessage();
                    header('Location: ../parent/parent-edit-profile.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Database error updating profile: ' . $e->getMessage();
                header('Location: ../parent/parent-edit-profile.php');
                exit();
            }
        }
    }
} else {
    $ins = $conn->prepare('INSERT INTO parent_profile (user_id, bio, location, profile_pic) VALUES (?, ?, ?, ?)');
    try {
        $ins->execute([$user_id, $bio, $location, $profile_pic_name]);
    } catch (PDOException $e) {
        if (stripos($e->getMessage(), 'foreign key') !== false || stripos($e->getMessage(), '1452') !== false) {
            if (repair_parent_profile_fk($conn)) {
                try {
                    $ins->execute([$user_id, $bio, $location, $profile_pic_name]);
                } catch (PDOException $e2) {
                    $_SESSION['error'] = 'Database error creating profile after repair: ' . $e2->getMessage();
                    header('Location: ../parent/parent-edit-profile.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Database FK error creating profile: ' . $e->getMessage();
                header('Location: ../parent/parent-edit-profile.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'Database error creating profile: ' . $e->getMessage();
            header('Location: ../parent/parent-edit-profile.php');
            exit();
        }
    }
}

if (!empty($profile_pic_name)) {
    $_SESSION['profile_pic'] = $profile_pic_name;
}

$_SESSION['success'] = 'Profile updated successfully.';
header('Location: /tutorlink/parent/parent-profile.php');
exit();
