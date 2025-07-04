<?php
include 'includes/db_config.php'; // Termasuk session_start() di sini

// Pengecekan sesi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Hanya admin yang bisa mengakses dashboard penuh ini
if ($_SESSION['role'] !== 'admin') {
    // Jika karyawan mencoba akses dashboard, redirect ke detail profilnya
    header("Location: employee_detail.php?email=" . urlencode($_SESSION['username']));
    exit();
}

// Ambil data untuk ringkasan
$total_karyawan = $conn->query("SELECT COUNT(*) FROM karyawan")->fetch_row()[0];
$karyawan_aktif = $conn->query("SELECT COUNT(*) FROM karyawan WHERE status = 'Aktif'")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Manajemen Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Manajemen Karyawan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employees.php">Data Karyawan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_employee.php">Tambah Karyawan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Daftar Akun</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-white">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger btn-sm text-white ms-2" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4 text-center">Dashboard Admin</h1>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Karyawan</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_karyawan; ?> Orang</h5>
                        <p class="card-text">Jumlah keseluruhan karyawan yang terdaftar.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Karyawan Aktif</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $karyawan_aktif; ?> Orang</h5>
                        <p class="card-text">Jumlah karyawan yang berstatus aktif.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <p>Selamat datang di Dashboard Admin. Anda memiliki kontrol penuh atas data karyawan.</p>
            <a href="employees.php" class="btn btn-lg btn-info">Lihat Data Karyawan</a>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container">
            <span class="text-muted">&copy; <?php echo date("Y"); ?> Manajemen Karyawan. Hak Cipta Dilindungi.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>