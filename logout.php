<?php
session_start(); // Pastikan session dimulai untuk menghancurkannya

// Hapus semua variabel sesi
$_SESSION = array();

// Hancurkan sesi
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>