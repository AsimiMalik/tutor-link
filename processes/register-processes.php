<?php
session_start();

require_once "../classes/database.php";
require_once "../classes/User.php";

$db = new Database();
$conn = $db->connect();

$user = new User($conn);

if(isset($_POST['register'])) {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'] ?? '';

    /* 🚨 VALIDATE ROLE (IMPORTANT FIX) */
    if($role !== 'tutor' && $role !== 'parent') {
        $_SESSION['error'] = "Invalid role selected!";
        header("Location: ../auth/register.php");
        exit();
    }

    /* hash password */
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $result = $user->register($fullname, $email, $hashedPassword, $role);

    if($result) {

        $_SESSION['success'] = "Account created successfully! Please login.";
        header("Location: ../auth/login.php");
        exit();

    } else {

        $_SESSION['error'] = "Registration failed! Email may already exist.";
        header("Location: ../auth/register.php");
        exit();
    }
}