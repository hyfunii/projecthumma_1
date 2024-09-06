<?php
include '../db/debeh.php';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['nisn'])) {
    $nisn = $_GET['nisn'];
    $stmt = $db->prepare("DELETE FROM hasil WHERE nisn = ?");
    $stmt->bind_param("s", $nisn);
    $result = $stmt->execute();
    if ($result) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die('Error: ' . $db->error);
    }
}

if (isset($_POST['delete_all'])) {
    $stmt = $db->prepare("DELETE FROM hasil");
    $result = $stmt->execute();
    if ($result) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die('Error: ' . $db->error);
    }
}

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
            padding-top: 2rem;
        }

        .btn-delete {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include '../navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Hasil Pendaftaran</h2>
        </div>
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Jurusan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['jurusan']); ?></td>
                            <td><?php echo htmlspecialchars($row['ket']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>