<?php
// Mulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include koneksi
require_once 'conn.php';

function connectDB() {
    global $conn; // gunakan koneksi dari conn.php
    return $conn;
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function register($name, $email, $password) {
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if (!$stmt) return false;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    return $stmt->execute();
}

function login($email, $password) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    if (!$stmt) return false;

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashedPassword);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user'] = ['id' => $id, 'name' => $name];
            return true;
        }
    }
    return false;
}
?>
