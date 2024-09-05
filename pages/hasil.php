<?php
include '../db/debeh.php';

// Handle individual record deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['nisn'])) {
    $nisn = $_GET['nisn'];
    $stmt = $db->prepare("DELETE FROM hasil WHERE nisn = ?");
    $stmt->bind_param("s", $nisn);
    $result = $stmt->execute();
    if ($result) {
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit();
    } else {
        die('Error: ' . $db->error);
    }
}

// Handle delete all
if (isset($_POST['delete_all'])) {
    $stmt = $db->prepare("DELETE FROM hasil");
    $result = $stmt->execute();
    if ($result) {
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit();
    } else {
        die('Error: ' . $db->error);
    }
}

// Fetch data for display
$query = "
    SELECT 
        h.nisn, 
        s.nama AS nama_siswa, 
        h.jurusan, 
        h.ket 
    FROM hasil h
    JOIN siswa s ON h.nisn = s.nisn
";
$result = $db->query($query);

if ($result === false) {
    die('Error: ' . $db->error);
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Seleksi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .container {
            padding-top: 3rem;
        }

        .btn-delete {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include '../navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Hasil Pendaftaran</h2>
            <div>
                <a href="admin.php" class="btn btn-primary">Kembali ke daftar seleksi</a>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="delete_all" class="btn btn-danger ms-2">Delete All</button>
                </form>
            </div>
        </div>
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Jurusan</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; // Nomor urut ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['jurusan']); ?></td>
                            <td><?php echo htmlspecialchars($row['ket']); ?></td>
                            <td>
                                <a href="?action=delete&nisn=<?php echo htmlspecialchars($row['nisn']); ?>" class="btn btn-danger btn-delete" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
