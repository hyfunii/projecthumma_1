<?php
include '../db/debeh.php'; // Pastikan ini menginisialisasi koneksi ke database

// Ambil data dari tabel hasil dengan join ke tabel siswa
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
    <link rel="stylesheet" href="path/to/your/bootstrap.css"> <!-- Update path if needed -->
    <style>
        .container{
            padding-top: 3rem;
        }
    </style>
</head>

<body>
    <?php include '../navbar.php'; ?>

    <div class="container mt-4">
        <h1>Hasil Seleksi</h1>
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
                        <td colspan="5" class="text-center">Tidak ada data untuk ditampilkan.</td>
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
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>