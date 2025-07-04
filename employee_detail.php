<?php
include 'includes/db_config.php'; // Termasuk session_start() di sini

// Pengecekan sesi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$employee = null;
$message = '';
$can_edit_delete = ($_SESSION['role'] === 'admin'); // Hanya admin yang bisa edit/delete dari halaman ini

// Logika untuk menentukan data karyawan yang akan ditampilkan
if ($can_edit_delete && isset($_GET['id'])) {
    // Admin bisa melihat berdasarkan ID
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM karyawan WHERE id = $id";
} elseif ($can_edit_delete && isset($_GET['email'])) {
    // Admin juga bisa melihat berdasarkan email (berguna untuk redirect dari login)
    $email_param = $conn->real_escape_string($_GET['email']);
    $sql = "SELECT * FROM karyawan WHERE email = '$email_param'";
} elseif ($_SESSION['role'] === 'karyawan') {
    // Karyawan hanya bisa melihat data mereka sendiri berdasarkan email di sesi
    $user_email = $conn->real_escape_string($_SESSION['username']); // Asumsi username karyawan adalah emailnya
    $sql = "SELECT * FROM karyawan WHERE email = '$user_email'";
} else {
    $message = "Akses ditolak atau ID karyawan tidak valid.";
}

if (isset($sql)) {
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        $message = "Data karyawan tidak ditemukan atau Anda tidak memiliki akses untuk melihat data ini.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Karyawan - Manajemen Karyawan</title>
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
                        <a class="nav-link <?php echo ($_SESSION['role'] == 'admin' ? '' : 'd-none'); ?>" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_SESSION['role'] == 'admin' ? '' : 'active'); ?>" aria-current="page" href="employees.php">Data Karyawan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_SESSION['role'] == 'admin' ? '' : 'd-none'); ?>" href="add_employee.php">Tambah Karyawan</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link <?php echo ($_SESSION['role'] == 'admin' ? '' : 'd-none'); ?>" href="register.php">Daftar Akun</a>
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
        <h1 class="mb-4 text-center">Detail Karyawan</h1>

        <?php if ($message) : ?>
            <div class="alert alert-warning" role="alert">
                <?php echo $message; ?>
            </div>
            <?php if ($_SESSION['role'] === 'admin') : ?>
                <a href="employees.php" class="btn btn-secondary">Kembali ke Daftar</a>
            <?php else : ?>
                 <a href="login.php" class="btn btn-secondary">Kembali ke Login</a>
            <?php endif; ?>

        <?php elseif ($employee) : ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?php echo htmlspecialchars($employee['nama_lengkap']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>ID Karyawan:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($employee['id']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Jabatan:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($employee['jabatan']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Departemen:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($employee['departemen']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Email:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($employee['email']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Nomor Telepon:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($employee['telepon']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Tanggal Masuk:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($employee['tanggal_masuk']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Status:</strong></div>
                        <div class="col-md-8">
                            <span class="badge <?php echo ($employee['status'] == 'Aktif') ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo htmlspecialchars($employee['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <?php if ($can_edit_delete) : ?>
                        <a href="edit_employee.php?id=<?php echo $employee['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_employee.php?id=<?php echo $employee['id']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');">Hapus</a>
                        <a href="employees.php" class="btn btn-secondary">Kembali ke Daftar</a>
                    <?php else : // Jika user adalah karyawan, hanya tampilkan tombol kembali ?>
                        <a href="logout.php" class="btn btn-secondary">Logout</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
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