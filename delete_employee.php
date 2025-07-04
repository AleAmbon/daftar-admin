<?php
include 'includes/db_config.php'; // Termasuk session_start() di sini

// Pengecekan sesi login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect ke halaman login jika tidak login atau bukan admin
    exit();
}

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    $sql = "DELETE FROM karyawan WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirect kembali ke halaman daftar karyawan setelah sukses
        header("Location: employees.php?status=deleted");
        exit();
    } else {
        // Redirect dengan pesan error jika gagal
        header("Location: employees.php?status=error&message=" . urlencode($conn->error));
        exit();
    }
} else {
    // Redirect jika ID tidak disediakan
    header("Location: employees.php?status=error&message=" . urlencode("ID karyawan tidak ditemukan."));
    exit();
}

$conn->close();
?>