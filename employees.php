<?php
include 'includes/db_config.php'; // Termasuk session_start() di sini

// Pengecekan sesi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Hanya admin yang bisa mengakses daftar semua karyawan
if ($_SESSION['role'] !== 'admin') {
    // Jika karyawan mencoba akses daftar semua karyawan, arahkan ke detail profilnya
    header("Location: employee_detail.php?email=" . urlencode($_SESSION['username']));
    exit();
}

$search = '';
$status_filter = '';

// Ambil parameter pencarian dan filter
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}
if (isset($_GET['status_filter'])) {
    $status_filter = $conn->real_escape_string($_GET['status_filter']);
}

// Bangun query SQL
$sql = "SELECT id, nama_lengkap, jabatan, departemen, email, telepon, status FROM karyawan WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (nama_lengkap LIKE '%$search%' OR jabatan LIKE '%$search%' OR departemen LIKE '%$search%')";
}
if (!empty($status_filter) && ($status_filter == 'Aktif' || $status_filter == 'Tidak Aktif')) {
    $sql .= " AND status = '$status_filter'";
}

$sql .= " ORDER BY nama_lengkap ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Karyawan - Manajemen Karyawan</title>
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
                        <a class="nav-link active" aria-current="page" href="employees.php">Data Karyawan</a>
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
        <h1 class="mb-4 text-center">Data Karyawan</h1>

        <div class="row mb-3">
            <div class="col-md-6">
                <form action="employees.php" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari nama, jabatan, departemen..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-outline-secondary">Cari</button>
                </form>
            </div>
            <div class="col-md-4">
                <form action="employees.php" method="GET" class="d-flex">
                    <select name="status_filter" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Aktif" <?php echo ($status_filter == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Tidak Aktif" <?php echo ($status_filter == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
            <div class="col-md-2 text-end">
                <a href="add_employee.php" class="btn btn-success">Tambah Karyawan</a>
            </div>
        </div>


        <?php if ($result->num_rows > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><a href="employee_detail.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nama_lengkap']); ?></a></td>
                                <td><?php echo htmlspecialchars($row['jabatan']); ?></td>
                                <td><?php echo htmlspecialchars($row['departemen']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['telepon']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['status'] == 'Aktif') ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="alert alert-info text-center" role="alert">
                Tidak ada data karyawan ditemukan.
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