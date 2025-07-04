<?php
include 'includes/db_config.php'; // Termasuk session_start() di sini

// Pengecekan sesi login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect ke halaman login jika tidak login atau bukan admin
    exit();
}

$message = '';
$message_type = '';
$employee = null;

// Ambil data karyawan saat halaman pertama kali dimuat atau setelah update
if (isset($_GET['id']) || (isset($_POST['id']) && $_SERVER["REQUEST_METHOD"] == "POST")) {
    $id_to_fetch = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
    $id_to_fetch = $conn->real_escape_string($id_to_fetch);

    $sql = "SELECT * FROM karyawan WHERE id = $id_to_fetch";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        $message = "Karyawan tidak ditemukan.";
        $message_type = "danger";
    }
}

// Proses update data ketika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $jabatan = $conn->real_escape_string($_POST['jabatan']);
    $departemen = $conn->real_escape_string($_POST['departemen']);
    $email = $conn->real_escape_string($_POST['email']);
    $telepon = $conn->real_escape_string($_POST['telepon']);
    $tanggal_masuk = $conn->real_escape_string($_POST['tanggal_masuk']);
    $status = $conn->real_escape_string($_POST['status']);

    // Cek apakah email sudah digunakan oleh karyawan lain (kecuali diri sendiri)
    $check_email_sql = "SELECT id FROM karyawan WHERE email = '$email' AND id != $id";
    $check_email_result = $conn->query($check_email_sql);
    if ($check_email_result->num_rows > 0) {
        $message = "Error: Email sudah terdaftar untuk karyawan lain.";
        $message_type = "danger";
    } else {
        $sql = "UPDATE karyawan SET
                nama_lengkap = '$nama_lengkap',
                jabatan = '$jabatan',
                departemen = '$departemen',
                email = '$email',
                telepon = '$telepon',
                tanggal_masuk = '$tanggal_masuk',
                status = '$status'
                WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            $message = "Data karyawan berhasil diperbarui!";
            $message_type = "success";
            // Refresh data karyawan yang ditampilkan setelah update berhasil
            $sql = "SELECT * FROM karyawan WHERE id = $id";
            $result = $conn->query($sql);
            $employee = $result->fetch_assoc();
        } else {
            $message = "Error memperbarui data: " . $conn->error;
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
    <title>Edit Karyawan - Manajemen Karyawan</title>
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
        <h1 class="mb-4 text-center">Edit Data Karyawan</h1>

        <?php if ($message) : ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($employee) : ?>
        <form action="edit_employee.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($employee['id']); ?>">
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($employee['nama_lengkap']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan</label>
                <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($employee['jabatan']); ?>">
            </div>
            <div class="mb-3">
                <label for="departemen" class="form-label">Departemen</label>
                <input type="text" class="form-control" id="departemen" name="departemen" value="<?php echo htmlspecialchars($employee['departemen']); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>">
            </div>
            <div class="mb-3">
                <label for="telepon" class="form-label">Nomor Telepon</label>
                <input type="tel" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars($employee['telepon']); ?>">
            </div>
            <div class="mb-3">
                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?php echo htmlspecialchars($employee['tanggal_masuk']); ?>">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Aktif" <?php echo ($employee['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Tidak Aktif" <?php echo ($employee['status'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="employees.php" class="btn btn-secondary">Kembali ke Daftar</a>
        </form>
        <?php else : ?>
            <div class="alert alert-warning text-center" role="alert">
                Data karyawan tidak tersedia untuk diedit. Pastikan ID karyawan benar.
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