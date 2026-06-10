<?php
session_start();

require_once "../classes/Database.php";
require_once "../classes/User.php";
require_once "../classes/Validate.php";

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

        if ((int)$loginUser['profile_completed'] === 0) {

            header("Location: ../tutor/edit-profile.php");
            exit();
        }

        header("Location: ../tutor/ttor-dashboard.php");
        exit();
    }

    if ($loginUser['role'] === 'parent') {

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