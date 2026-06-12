<?php
session_start();

require_once "../classes/Database.php";
require_once "../classes/User.php";
require_once "../classes/Validate.php";
require_once __DIR__ . '/../includes/csrf.php';

if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
    $_SESSION['error'] = 'Invalid CSRF token.';
    header("Location: ../auth/register.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$user = new User($conn);

if (isset($_POST['register'])) {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'] ?? '';

    // Validate fullname
    $error = Validate::validateFullName($fullname);

    if ($error) {
        $_SESSION['error'] = $error;
        header("Location: ../auth/register.php");
        exit();
    }

    // Validate email
    $error = Validate::validateEmail($email);

    if ($error) {
        $_SESSION['error'] = $error;
        header("Location: ../auth/register.php");
        exit();
    }

    // Validate password
    $error = Validate::validatePassword($password);

    if ($error) {
        $_SESSION['error'] = $error;
        header("Location: ../auth/register.php");
        exit();
    }

    // Validate confirm password
    $error = Validate::validateConfirmPassword(
        $password,
        $confirm_password
    );

    if ($error) {
        $_SESSION['error'] = $error;
        header("Location: ../auth/register.php");
        exit();
    }

    // Validate role
    $error = Validate::validateRole($role);

    if ($error) {
        $_SESSION['error'] = $error;
        header("Location: ../auth/register.php");
        exit();
    }

    $result = $user->register(
        $fullname,
        $email,
        $password,
        $role
    );

    if ($result) {

        $_SESSION['success'] =
            "Account created successfully! Please login.";

        header("Location: ../auth/login.php");
        exit();
    }

    $_SESSION['error'] =
        "Registration failed! Email may already exist.";

    header("Location: ../auth/register.php");
    exit();
}