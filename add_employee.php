<?php
include 'includes/db_config.php'; // Termasuk session_start() di sini

// Pengecekan sesi login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect ke halaman login jika tidak login atau bukan admin
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $jabatan = $conn->real_escape_string($_POST['jabatan']);
    $departemen = $conn->real_escape_string($_POST['departemen']);
    $email = $conn->real_escape_string($_POST['email']);
    $telepon = $conn->real_escape_string($_POST['telepon']);
    $tanggal_masuk = $conn->real_escape_string($_POST['tanggal_masuk']);
    $status = $conn->real_escape_string($_POST['status']);

    // Cek apakah email sudah ada di database karyawan
    $check_email_sql = "SELECT id FROM karyawan WHERE email = '$email'";
    $check_email_result = $conn->query($check_email_sql);
    if ($check_email_result->num_rows > 0) {
        $message = "Error: Email sudah terdaftar untuk karyawan lain.";
        $message_type = "danger";
    } else {
        $sql = "INSERT INTO karyawan (nama_lengkap, jabatan, departemen, email, telepon, tanggal_masuk, status)
                VALUES ('$nama_lengkap', '$jabatan', '$departemen', '$email', '$telepon', '$tanggal_masuk', '$status')";

        if ($conn->query($sql) === TRUE) {
            $message = "Karyawan baru berhasil ditambahkan!";
            $message_type = "success";
            // Kosongkan form setelah sukses
            $_POST = array();
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
            $message_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan Baru - Manajemen Karyawan</title>
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
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employees.php">Data Karyawan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="add_employee.php">Tambah Karyawan</a>
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
        <h1 class="mb-4 text-center">Tambah Karyawan Baru</h1>

        <?php if ($message) : ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="add_employee.php" method="POST">
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan</label>
                <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($_POST['jabatan'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="departemen" class="form-label">Departemen</label>
                <input type="text" class="form-control" id="departemen" name="departemen" value="<?php echo htmlspecialchars($_POST['departemen'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="telepon" class="form-label">Nomor Telepon</label>
                <input type="tel" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars($_POST['telepon'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?php echo htmlspecialchars($_POST['tanggal_masuk'] ?? date('Y-m-d')); ?>">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Aktif" <?php echo (($_POST['status'] ?? 'Aktif') == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Tidak Aktif" <?php echo (($_POST['status'] ?? 'Aktif') == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Karyawan</button>
            <a href="employees.php" class="btn btn-secondary">Kembali ke Daftar</a>
        </form>
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