<?php
session_start();

require_once "../classes/Database.php";
require_once "../classes/User.php";
require_once "../classes/Validate.php";
require_once __DIR__ . '/../includes/csrf.php';

if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
    $_SESSION['error'] = 'Invalid CSRF token.';
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$user = new User($conn);

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate email format
    $error = Validate::validateEmail($email);

    if ($error) {
        $_SESSION['error'] = $error;
        header("Location: ../auth/login.php");
        exit();
    }

    $loginUser = $user->login($email, $password);

    if (!$loginUser) {
        $_SESSION['error'] = "Invalid email or password!";
        header("Location: ../auth/login.php");
        exit();
    }

    $_SESSION['user_id'] = $loginUser['id'];
    $_SESSION['fullname'] = $loginUser['fullname'];
    $_SESSION['role'] = $loginUser['role'];

    $_SESSION['success'] = "Login successful! Welcome back.";

    if ($loginUser['role'] === 'tutor') {

        // load tutor profile picture into session so it shows immediately
        $stmtPic = $conn->prepare("SELECT profile_pic FROM tutor_profile WHERE user_id = ?");
        $stmtPic->execute([$loginUser['id']]);
        $picRow = $stmtPic->fetch(PDO::FETCH_ASSOC);
        $_SESSION['profile_pic'] = $picRow['profile_pic'] ?? '';

        // Determine if tutor has completed their profile by checking tutor_profile
        $stmtProfile = $conn->prepare("SELECT id FROM tutor_profile WHERE user_id = ? LIMIT 1");
        $stmtProfile->execute([$loginUser['id']]);
        $hasProfile = (bool)$stmtProfile->fetch(PDO::FETCH_ASSOC);

        if (!$hasProfile) {
            // first-time tutor without a profile → send to edit profile to complete it
            header("Location: ../tutor/tutor-edit-profile.php");
            exit();
        }

        header("Location: ../tutor/tutor-dashboard.php");
        exit();
    }

    if ($loginUser['role'] === 'parent') {
        // load parent profile picture into session so it shows immediately
        $stmtPic = $conn->prepare("SELECT profile_pic FROM parent_profile WHERE user_id = ?");
        $stmtPic->execute([$loginUser['id']]);
        $picRow = $stmtPic->fetch(PDO::FETCH_ASSOC);
        $_SESSION['profile_pic'] = $picRow['profile_pic'] ?? '';

        // Determine if parent has completed their profile by checking parent_profile
        $stmtProfile = $conn->prepare("SELECT id FROM parent_profile WHERE user_id = ? LIMIT 1");
        $stmtProfile->execute([$loginUser['id']]);
        $hasProfile = (bool)$stmtProfile->fetch(PDO::FETCH_ASSOC);

        if (!$hasProfile) {
            // first-time parent without a profile → send to edit profile to complete it
            header("Location: ../parent/parent-edit-profile.php");
            exit();
        }

        header("Location: ../parent/parent-dashboard.php");
        exit();
    }

    if ($loginUser['role'] === 'admin') {

        header("Location: ../admin/dashboard.php");
        exit();
    }

    $_SESSION['error'] = "Unknown user role.";
    header("Location: ../auth/login.php");
    exit();
}