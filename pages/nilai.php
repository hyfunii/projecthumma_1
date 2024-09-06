<?php
include '../db/debeh.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $nisn = $_GET['delete'];
    if ($nisn) {
        $delete_query = "DELETE FROM nilai WHERE nisn = ?";
        $stmt = $db->prepare($delete_query);
        $stmt->bind_param('s', $nisn);
        $stmt->execute();
        $stmt->close();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle edit request
if (isset($_POST['update'])) {
    $nisn = $_POST['nisn'];
    $nilai_rata = $_POST['nilai_rata'];

    if ($nisn && $nilai_rata) {
        $update_query = "UPDATE nilai SET nilai_rata = ? WHERE nisn = ?";
        $stmt = $db->prepare($update_query);
        $stmt->bind_param('ds', $nilai_rata, $nisn);
        $stmt->execute();
        $stmt->close();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle add request
if (isset($_POST['add'])) {
    $nisn = $_POST['nisn'];
    $nilai_rata = $_POST['nilai_rata'];

    if ($nisn && $nilai_rata) {
        $insert_query = "INSERT INTO nilai (nisn, nilai_rata) VALUES (?, ?)";
        $stmt = $db->prepare($insert_query);
        $stmt->bind_param('sd', $nisn, $nilai_rata);
        $stmt->execute();
        $stmt->close();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

$orderBy = $_GET['sort'] ?? 'n.nisn';
$orderDir = $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';

$query = "
    SELECT
        n.nisn,
        n.nilai_rata,
        s.nama AS Nama
    FROM nilai n
    JOIN siswa s ON n.nisn = s.nisn
    ORDER BY $orderBy $orderDir
";

$result = $db->query($query);

$rows = [];
while ($data_show = $result->fetch_assoc()) {
    $rows[] = $data_show;
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Rata-Rata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sortable {
            cursor: pointer;
        }

        .sortable::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
        }

        .asc::after {
            border-bottom: 5px solid #000;
        }

        .desc::after {
            border-top: 5px solid #000;
        }

        .container {
            padding-top: 2rem;
        }

        .btn-edit,
        .btn-delete,
        .btn-add {
            margin: 0 0.5rem;
        }
    </style>
</head>

<body>
    <?php include '../navbaradmin.php'; ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Data Nilai</h2>
            <button class="btn btn-primary btn-add btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Nilai Baru</button>
        </div>
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th class="sortable" data-sort="n.nisn">NISN</th>
                    <th class="sortable" data-sort="s.nama">Nama</th>
                    <th class="sortable" data-sort="n.nilai_rata">Nilai Rata-Rata</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php else: ?>
                    <?php $numb = 1; ?>
                    <?php foreach ($rows as $data_show): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($numb++); ?></td>
                            <td><?php echo htmlspecialchars($data_show['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($data_show['Nama']); ?></td>
                            <td><?php echo htmlspecialchars($data_show['nilai_rata']); ?></td>
                            <td>
                                <a href="#editModal<?php echo htmlspecialchars($data_show['nisn']); ?>" data-bs-toggle="modal"
                                    class="btn btn-warning btn-sm btn-edit">Edit</a>
                                <a href="?delete=<?php echo urlencode($data_show['nisn']); ?>"
                                    class="btn btn-danger btn-delete btn-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Delete</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?php echo htmlspecialchars($data_show['nisn']); ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo htmlspecialchars($data_show['nisn']); ?>"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="editModalLabel<?php echo htmlspecialchars($data_show['nisn']); ?>">Edit Nilai
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form method="post">
                                        <div class="modal-body">
                                            <input type="hidden" name="nisn"
                                                value="<?php echo htmlspecialchars($data_show['nisn']); ?>">
                                            <div class="mb-3">
                                                <label for="nilai_rata" class="form-label">Nilai Rata-Rata</label>
                                                <input type="number" class="form-control" id="nilai_rata" name="nilai_rata"
                                                    value="<?php echo htmlspecialchars($data_show['nilai_rata']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" required>
                        </div>
                        <div class="mb-3">
                            <label for="nilai_rata" class="form-label">Nilai Rata-Rata</label>
                            <input type="number" class="form-control" id="nilai_rata" name="nilai_rata" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add" class="btn btn-primary btn-sm">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const currentSort = urlParams.get('sort');
            const currentDir = urlParams.get('dir') || 'asc';

            document.querySelectorAll('th[data-sort]').forEach(th => {
                const sortKey = th.getAttribute('data-sort');
                th.classList.toggle(currentDir, sortKey === currentSort);
                th.addEventListener('click', () => {
                    const newDir = (currentSort === sortKey && currentDir === 'asc') ? 'desc' : 'asc';
                    urlParams.set('sort', sortKey);
                    urlParams.set('dir', newDir);
                    window.location.search = urlParams.toString();
                });
            });
        });
    </script>
</body>

</html>