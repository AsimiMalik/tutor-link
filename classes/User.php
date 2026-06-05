<?php

class User {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // REGISTER
    public function register($fullname, $email, $password, $role) {

        // check email
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if($check->rowCount() > 0) {
            return false;
        }

        // IMPORTANT: ensure role is not empty
        if(empty($role)) {
            return false;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO users (fullname, email, password, role)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([$fullname, $email, $hashed, $role]);
    }

    // LOGIN
    public function login($email, $password) {

        $stmt = $this->conn->prepare("
            SELECT * FROM users WHERE email = ?
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])) {

            // safety fix (important)
            if(empty($user['role'])) {
                return false;
            }

            return $user;
        }

        return false;
    }
}