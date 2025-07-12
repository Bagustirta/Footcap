<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if ($password !== $confirm_password) {
    echo "Password dan konfirmasi tidak cocok.";
    exit;
  }

  // Cek apakah email sudah terdaftar
  $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    echo "Email sudah terdaftar.";
    exit;
  }

  // Enkripsi password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Simpan data ke database
  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $hashedPassword);

  if ($stmt->execute()) {
    header("Location: login.php?success=1");
    exit;
  } else {
    echo "Gagal mendaftar: " . $conn->error;
  }
}
