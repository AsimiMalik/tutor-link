<?php
session_start();

// Temporary debug helpers — remove or disable in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
----------------------------------------------------
CHECK LOGIN
----------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        header('Location: ../tutor/tutor-edit-profile.php');
        exit();
    }
}

/*
----------------------------------------------------
CHECK ROLE
----------------------------------------------------
*/
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login.php");
    exit();
}

/*
----------------------------------------------------
CONNECT DATABASE
----------------------------------------------------
*/
require_once "../classes/Database.php";

$db = new Database();
$conn = $db->connect();

// Ensure `qualification_file` column exists (auto-migrate if missing)
try {
    $colStmt = $conn->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'tutor_profile' AND COLUMN_NAME = 'qualification_file' LIMIT 1");
    $cfg = include __DIR__ . '/../config/db-connect.php';
    $colStmt->execute([$cfg['dbname']]);
    $col = $colStmt->fetch(PDO::FETCH_ASSOC);
    if (!$col) {
        // attempt to add column
        $conn->exec("ALTER TABLE tutor_profile ADD COLUMN qualification_file VARCHAR(255) NULL AFTER qualification");
    }
} catch (Exception $e) {
    // If we can't alter the table (permissions etc.), continue silently; upload will still save file on disk
}

/*
----------------------------------------------------
GET USER ID
----------------------------------------------------
*/
$user_id = $_SESSION['user_id'];

/*
----------------------------------------------------
COLLECT FORM DATA
----------------------------------------------------
*/
$bio = $_POST['bio'] ?? '';
$experience = $_POST['experience'] ?? '';
$location = $_POST['location'] ?? '';
$hourly_rate = $_POST['hourly_rate'] ?? 0;
// subjects array from form
$subjects_input = $_POST['subjects'] ?? [];
// qualification
$qualification = $_POST['qualification'] ?? '';

/*
----------------------------------------------------
HANDLE PROFILE IMAGE UPLOAD
----------------------------------------------------
*/
$profile_pic_name = null;

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {

    $file = $_FILES['profile_pic'];

    $file_name = $user_id . '_' . time() . "_" . basename($file['name']);
    // use absolute path to avoid relative path issues
    $target_dir = __DIR__ . '/../uploads/';

    // ensure uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_file = $target_dir . $file_name;

    // move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        $profile_pic_name = $file_name;
    } else {
        $_SESSION['error'] = 'Unable to save uploaded file.';
    }
}

// qualification file upload (optional)
$qualification_file_name = null;
if (isset($_FILES['qualification_file'])) {
    $qfile = $_FILES['qualification_file'];
    // handle common upload errors
    if (!isset($qfile['error']) || is_array($qfile['error'])) {
        $_SESSION['error'] = 'Invalid file upload.';
        header('Location: ../tutor/tutor-edit-profile.php'); exit();
    }
    if ($qfile['error'] !== UPLOAD_ERR_OK && $qfile['error'] !== 0) {
        // map error
        $errMap = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
        ];
        $msg = $errMap[$qfile['error']] ?? 'Unknown upload error.';
        $_SESSION['error'] = 'Qualification upload error: ' . $msg;
        header('Location: ../tutor/tutor-edit-profile.php'); exit();
    }
    // proceed only if upload OK and file provided
    if ($qfile['error'] == UPLOAD_ERR_OK || $qfile['error'] == 0) {
        $qext = strtolower(pathinfo($qfile['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf','doc','docx','jpg','jpeg','png'];
        if (!in_array($qext, $allowed)) {
            $_SESSION['error'] = 'Invalid qualification file type. Allowed: pdf, doc, docx, jpg, png';
            header('Location: ../tutor/tutor-edit-profile.php'); exit();
        }
        $qfile_name = $user_id . '_' . time() . "_qual_" . basename($qfile['name']);
        $qtarget_dir = __DIR__ . '/../uploads/qualifications/';
        if (!is_dir($qtarget_dir)) mkdir($qtarget_dir, 0755, true);
        $qtarget_file = $qtarget_dir . $qfile_name;
        if (move_uploaded_file($qfile['tmp_name'], $qtarget_file)) {
            $qualification_file_name = $qfile_name;
        } else {
            $_SESSION['error'] = 'Unable to save qualification file to disk.';
            header('Location: ../tutor/tutor-edit-profile.php'); exit();
        }
    }
}

/*
----------------------------------------------------
CHECK IF PROFILE EXISTS
----------------------------------------------------
*/
$stmt = $conn->prepare("SELECT id FROM tutor_profile WHERE user_id = ?");
$stmt->execute([$user_id]);
$exists = $stmt->fetch();

// If there's no qualification_file recorded but a file exists in uploads/qualifications prefixed with userId_, link the latest one.
try {
    $qstmt = $conn->prepare("SELECT qualification_file FROM tutor_profile WHERE user_id = ? LIMIT 1");
    $qstmt->execute([$user_id]);
    $qrow = $qstmt->fetch(PDO::FETCH_ASSOC);
    if (!$qrow || empty($qrow['qualification_file'])) {
        $qualDir = __DIR__ . '/../uploads/qualifications';
        if (is_dir($qualDir)) {
            $files = array_values(array_filter(scandir($qualDir), function($f){ return $f !== '.' && $f !== '..' && is_file(__DIR__.'/../uploads/qualifications/'.$f); }));
            // find files starting with userId_ prefix
            $pref = $user_id . '_';
            $candidates = array_filter($files, function($f) use ($pref){ return strpos($f, $pref) === 0; });
            if (!empty($candidates)) {
                // pick latest by modified time
                usort($candidates, function($a,$b) use ($qualDir){ return filemtime($qualDir.'/'.$b) - filemtime($qualDir.'/'.$a); });
                $chosen = $candidates[0];
                if ($exists) {
                    $up = $conn->prepare('UPDATE tutor_profile SET qualification_file = ? WHERE user_id = ?');
                    $up->execute([$chosen, $user_id]);
                } else {
                    $ins = $conn->prepare('INSERT INTO tutor_profile (user_id, qualification_file, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
                    $ins->execute([$user_id, $chosen]);
                    // refresh $exists for downstream logic
                    $stmt = $conn->prepare("SELECT id FROM tutor_profile WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    $exists = $stmt->fetch();
                }
            }
        }
    }
} catch (Exception $e) {
    // ignore reconciliation errors
}

/*
----------------------------------------------------
IF PROFILE EXISTS → UPDATE
----------------------------------------------------
*/
if ($exists) {

        if ($profile_pic_name) {
            $stmt = $conn->prepare(
                "UPDATE tutor_profile \n"
                . "SET bio = ?, qualification = ?, experience = ?, location = ?, hourly_rate = ?, profile_pic = ?, qualification_file = ?\n"
                . "WHERE user_id = ?"
            );

            $stmt->execute([
                $bio,
                $qualification,
                $experience,
                $location,
                $hourly_rate,
                $profile_pic_name,
                $qualification_file_name,
                $user_id
            ]);

        } else {
            // handle case where only qualification file uploaded
            if ($qualification_file_name) {
                $stmt = $conn->prepare("UPDATE tutor_profile SET bio = ?, qualification = ?, experience = ?, location = ?, hourly_rate = ?, qualification_file = ? WHERE user_id = ?");
                $stmt->execute([$bio,$qualification,$experience,$location,$hourly_rate,$qualification_file_name,$user_id]);
            } else {
                $stmt = $conn->prepare(
                    "UPDATE tutor_profile \n"
                    . "SET bio = ?, qualification = ?, experience = ?, location = ?, hourly_rate = ?\n"
                    . "WHERE user_id = ?"
                );
                $stmt->execute([
                    $bio,
                    $qualification,
                    $experience,
                    $location,
                    $hourly_rate,
                    $user_id
                ]);
            }
        }

}

/*
----------------------------------------------------
IF PROFILE DOES NOT EXIST → INSERT
----------------------------------------------------
*/
else {

    $stmt = $conn->prepare(
        "INSERT INTO tutor_profile \n"
        . "(user_id, bio, qualification, experience, location, hourly_rate, profile_pic, qualification_file)\n"
        . "VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $user_id,
        $bio,
        $qualification,
        $experience,
        $location,
        $hourly_rate,
        $profile_pic_name,
        $qualification_file_name
    ]);
}

// save tutor subjects assignments (if table exists)
try {
    if (!is_array($subjects_input)) $subjects_input = [];
    // sanitize ints
    $subject_ids = array_filter(array_map(function($v){return (int)$v;}, $subjects_input));
    // use TutorSubject class if available
    if (file_exists(__DIR__ . '/../classes/TutorSubject.php')) {
        require_once __DIR__ . '/../classes/TutorSubject.php';
        $ts = new TutorSubject($conn);
        $ts->assignSubjects($user_id, $subject_ids);
    } else {
        // fallback: direct DB operations
        $conn->beginTransaction();
        $del = $conn->prepare("DELETE FROM tutor_subjects WHERE tutor_id = ?");
        $del->execute([$user_id]);
        if (!empty($subject_ids)) {
            $ins = $conn->prepare("INSERT INTO tutor_subjects (tutor_id, subject_id) VALUES (?, ?)");
            foreach ($subject_ids as $sid) {
                if ($sid <= 0) continue;
                $ins->execute([$user_id, $sid]);
            }
        }
        $conn->commit();
    }
} catch (PDOException $e) {
    // silently continue; optional: log or set flash
}

// mark user's profile as completed in users table (if column exists)
try {
    $stmt = $conn->prepare("UPDATE users SET profile_completed = 1 WHERE id = ?");
    $stmt->execute([$user_id]);
} catch (PDOException $e) {
    // likely the `profile_completed` column doesn't exist in users table — ignore silently
}
// refresh session profile_pic so the new image becomes the default immediately
if (!empty($profile_pic_name)) {
    $_SESSION['profile_pic'] = $profile_pic_name;
} else {
    $stmt = $conn->prepare("SELECT profile_pic FROM tutor_profile WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['profile_pic'] = $r['profile_pic'] ?? '';
}

// refresh session profile_pic so the new image becomes the default immediately
if (!empty($profile_pic_name)) {
    $_SESSION['profile_pic'] = $profile_pic_name;
}

$_SESSION['success'] = 'Profile updated successfully.';
// redirect to tutor profile page after completing profile (so navbar is visible)
header("Location: /brilliance/tutor/tutor-profile.php");
exit();
?>