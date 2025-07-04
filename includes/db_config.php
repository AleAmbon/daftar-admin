<?php
// Pastikan session dimulai di awal setiap request yang membutuhkan session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root"; // Default username untuk XAMPP/WAMP/MAMP
$password = "";     // Default password (kosong)
$dbname = "db_karyawan"; // Nama database yang kamu buat di phpMyAdmin

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>