<?php
include 'includes/db_config.php'; // Termasuk session_start() di sini

// Opsional: Batasi siapa yang bisa mendaftar (misal: hanya admin yang bisa masuk ke halaman ini)
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     // Jika tidak login atau bukan admin, redirect ke login
//     header("Location: login.php");
//     exit();
// }

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $conn->real_escape_string($_POST['role']);

    if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
        $message = "Semua kolom harus diisi.";
        $message_type = "danger";
    } elseif ($password !== $confirm_password) {
        $message = "Konfirmasi password tidak cocok.";
        $message_type = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Password minimal 6 karakter.";
        $message_type = "danger";
    } else {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah username sudah ada
        $check_sql = "SELECT id FROM users WHERE username = '$username'";
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            $message = "Username sudah terdaftar. Gunakan username lain.";
            $message_type = "danger";
        } else {
            $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";
            if ($conn->query($sql) === TRUE) {
                $message = "Akun berhasil didaftarkan! Silakan <a href='login.php'>Login</a>.";
                $message_type = "success";
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
                $message_type = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="register-page-body">
    <div class="auth-container">
        <h2 class="text-center mb-4">Daftar Akun Baru</h2>

        <?php if ($message) : ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username (Email atau ID Karyawan)</label>
                <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Daftar Sebagai</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="karyawan">Karyawan</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
        <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>