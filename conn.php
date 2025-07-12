<?php
$host     = 'localhost';     // atau IP database server
$username = 'root';          // sesuaikan dengan user DB kamu
$password = '';              // sesuaikan dengan password DB kamu
$database = 'e-commerce'; // nama database

$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>