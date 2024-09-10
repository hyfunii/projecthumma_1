<?php
include '../db/debeh.php';

$status = '';
$toastType = '';
$toastMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $nisn = $_POST['nisn'];
        $nama = $_POST['nama'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $alamat = $_POST['alamat'];
        $ortu = $_POST['ortu'];

        $check_sql = "SELECT COUNT(*) AS count FROM siswa WHERE nisn=?";
        $stmt = $db->prepare($check_sql);
        $stmt->bind_param('s', $nisn);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $status = 'error';
        } else {
            $sql = "INSERT INTO siswa (nisn, nama, tgl_lahir, alamat, ortu) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('sssss', $nisn, $nama, $tgl_lahir, $alamat, $ortu);
            $stmt->execute();
            $stmt->close();
            $status = 'success';
        }
        header('Location: siswa.php?status=' . $status);
        exit;
    } elseif ($action == 'edit') {
        $id_siswa = $_POST['id_siswa'];
        $nama = $_POST['nama'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $alamat = $_POST['alamat'];
        $ortu = $_POST['ortu'];

        $sql = "UPDATE siswa SET nama=?, tgl_lahir=?, alamat=?, ortu=? WHERE id_siswa=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssssi', $nama, $tgl_lahir, $alamat, $ortu, $id_siswa);
        $stmt->execute();
        $stmt->close();

        $status = 'success';
        header('Location: siswa.php?status=' . $status);
        exit;
    } elseif ($action == 'delete') {
        $id_siswa = $_POST['id_siswa'];

        $sql = "DELETE FROM siswa WHERE id_siswa=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id_siswa);
        $stmt->execute();
        $stmt->close();

        $status = 'success';
        header('Location: siswa.php?status=' . $status);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            padding-top: 2rem;
        }

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
    </style>
</head>

<body>
    <?php include '../navbaradmin.php' ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Data Siswa</h2>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Tambah
                Siswa</button>
        </div>
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>NISN</th>
                    <th>Nama</th>
                    <th>Tanggal Lahir</th>
                    <th>Alamat</th>
                    <th>Ortu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $numb = 1;
                $result = $db->query("SELECT * FROM siswa");
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                if (empty($rows)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($numb++); ?></td>
                            <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['tgl_lahir']); ?></td>
                            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                            <td><?php echo htmlspecialchars($row['ortu']); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="<?php echo htmlspecialchars($row['id_siswa']); ?>"
                                    data-nisn="<?php echo htmlspecialchars($row['nisn']); ?>"
                                    data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
                                    data-tgl_lahir="<?php echo htmlspecialchars($row['tgl_lahir']); ?>"
                                    data-alamat="<?php echo htmlspecialchars($row['alamat']); ?>"
                                    data-ortu="<?php echo htmlspecialchars($row['ortu']); ?>">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="deleteSiswa(<?php echo htmlspecialchars($row['id_siswa']); ?>)">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nisn">NISN</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" required>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="tgl_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="ortu">Orang Tua</label>
                            <input type="text" class="form-control" id="ortu" name="ortu" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="action" value="add">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_siswa" name="id_siswa">
                        <div class="form-group">
                            <label for="edit_nisn">NISN</label>
                            <input type="text" class="form-control" id="edit_nisn" name="nisn" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit_nama">Nama</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_tgl_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="edit_tgl_lahir" name="tgl_lahir" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_alamat">Alamat</label>
                            <textarea class="form-control" id="edit_alamat" name="alamat" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_ortu">Orang Tua</label>
                            <input type="text" class="form-control" id="edit_ortu" name="ortu" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="action" value="edit">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast-success" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Sukses</strong>
                <small>Baru saja</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Operasi berhasil!
            </div>
        </div>
        <div id="toast-error" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Error</strong>
                <small>Baru saja</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Operasi gagal, coba lagi!
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                showToast('success');
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                showToast('error');
            <?php endif; ?>
        });

        function showToast(type) {
            var toast = document.getElementById('toast-' + type);
            var toastInstance = new bootstrap.Toast(toast);
            toastInstance.show();
        }

        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#edit_id_siswa').val(button.data('id'));
            modal.find('#edit_nisn').val(button.data('nisn'));
            modal.find('#edit_nama').val(button.data('nama'));
            modal.find('#edit_tgl_lahir').val(button.data('tgl_lahir'));
            modal.find('#edit_alamat').val(button.data('alamat'));
            modal.find('#edit_ortu').val(button.data('ortu'));
        });

        function deleteSiswa(id_siswa) {
            if (confirm('Apakah Anda yakin ingin menghapus siswa ini?')) {
                var form = document.createElement('form');
                form.method = 'post';
                form.action = 'siswa.php';

                var inputAction = document.createElement('input');
                inputAction.type = 'hidden';
                inputAction.name = 'action';
                inputAction.value = 'delete';
                form.appendChild(inputAction);

                var inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id_siswa';
                inputId.value = id_siswa;
                form.appendChild(inputId);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>