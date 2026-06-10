<?php

class Validate {

    public static function validateFullName($fullname) {

        if (empty(trim($fullname))) {
            return "Full name is required.";
        }

        if (strlen(trim($fullname)) < 2) {
            return "Full name must be at least 2 characters.";
        }

        return null;
    }

    public static function validateEmail($email) {

        if (empty(trim($email))) {
            return "Email is required.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }

        return null;
    }

    public static function validatePassword($password) {

        if (empty($password)) {
            return "Password is required.";
        }

        if (strlen($password) < 8) {
            return "Password must be at least 8 characters.";
        }

        return null;
    }

    public static function validateConfirmPassword($password, $confirm_password) {

        if ($password !== $confirm_password) {
            return "Passwords do not match.";
        }

        return null;
    }

    public static function validateRole($role) {

        if ($role !== 'tutor' && $role !== 'parent') {
            return "Invalid role selected.";
        }

        return null;
    }
}