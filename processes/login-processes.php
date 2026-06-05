<?php
session_start();

require_once "../classes/database.php";
require_once "../classes/User.php";

$db = new Database();
$conn = $db->connect();

$user = new User($conn);

if(isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $loginUser = $user->login($email, $password);

    if($loginUser) {

        /* ✅ SESSION DATA */
        $_SESSION['user_id'] = $loginUser['id'];
        $_SESSION['fullname'] = $loginUser['fullname'];
        $_SESSION['role'] = $loginUser['role'];

        /* OPTIONAL SUCCESS MESSAGE */
        $_SESSION['success'] = "Login successful! Welcome back.";

        $_SESSION['user_id'] = $loginUser['id'];
$_SESSION['fullname'] = $loginUser['fullname'];
$_SESSION['role'] = $loginUser['role'];
        

        /* ✅ ROLE-BASED REDIRECT */
        $role = $loginUser['role'];

        if($loginUser['role'] == 'tutor') {

            if($loginUser['profile_completed'] == 0){
                header("Location: ../tutor/edit-profile.php");
                exit();
            }
        
            header("Location: ../tutor/dashboard.php");
            exit();
        
        } elseif($loginUser['role'] == 'parent') {
        
            header("Location: ../parent/dashboard.php");
            exit();
        }

        elseif($role === 'parent') {
            header("Location: ../parent/p-dashboard.php");
            exit();
        }

        /* fallback (safe default) */
        else {
            header("Location: ../auth/login.php");
            exit();
        }

    } else {

        $_SESSION['error'] = "Invalid email or password!";
        header("Location: ../auth/login.php");
        exit();
    }
}