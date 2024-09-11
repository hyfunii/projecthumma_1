<?php
include '../db/debeh.php';

$toastType = '';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['nisn'])) {
    $nisn = $_GET['nisn'];
    $stmt = $db->prepare("DELETE FROM hasil WHERE nisn = ?");
    $stmt->bind_param("s", $nisn);
    $result = $stmt->execute();
    if ($result) {
        $toastType = 'success'; // Set toast type to success
    } else {
        $toastType = 'error'; // Set toast type to error
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?toast=' . $toastType);
    exit();
}

if (isset($_POST['delete_all'])) {
    $stmt = $db->prepare("DELETE FROM hasil");
    $result = $stmt->execute();
    if ($result) {
        $toastType = 'success'; // Set toast type to success
    } else {
        $toastType = 'error'; // Set toast type to error
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?toast=' . $toastType);
    exit();
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
    <?php include '../navbaradmin.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Hasil Pendaftaran</h2>
            <div>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="delete_all" class="btn btn-danger mb-3"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus semua data siswa?')">Hapus
                        Semua</button>
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
                    <?php $no = 1; ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($row['jurusan']); ?></td>
                            <td><?php echo htmlspecialchars($row['ket']); ?></td>
                            <td>
                                <a href="?action=delete&nisn=<?php echo htmlspecialchars($row['nisn']); ?>"
                                    class="btn btn-danger btn-delete"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Data berhasil dihapus!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
        <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Terjadi kesalahan saat menghapus data.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const toastType = urlParams.get('toast');

            if (toastType === 'success') {
                const toast = new bootstrap.Toast(document.getElementById('successToast'));
                toast.show();
            } else if (toastType === 'error') {
                const toast = new bootstrap.Toast(document.getElementById('errorToast'));
                toast.show();
            }
        });
    </script>
</body>

</html>