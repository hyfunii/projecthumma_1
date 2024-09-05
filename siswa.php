<?php
include 'debeh.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $nisn = $_POST['nisn'];
        $nama = $_POST['nama'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $alamat = $_POST['alamat'];
        $ortu = $_POST['ortu'];

        $sql = "INSERT INTO siswa (nisn, nama, tgl_lahir, alamat, ortu) VALUES ('$nisn', '$nama', '$tgl_lahir', '$alamat', '$ortu')";
        $db->query($sql);
        
        header('Location: siswa.php');
        exit;
    } elseif ($action == 'edit') {
        $id_siswa = $_POST['id_siswa'];
        $nama = $_POST['nama'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $alamat = $_POST['alamat'];
        $ortu = $_POST['ortu'];

        $sql = "UPDATE siswa SET nama='$nama', tgl_lahir='$tgl_lahir', alamat='$alamat', ortu='$ortu' WHERE id_siswa='$id_siswa'";
        $db->query($sql);
        
        header('Location: siswa.php');
        exit;
    } elseif ($action == 'delete') {
        $id_siswa = $_POST['id_siswa'];

        $sql = "DELETE FROM siswa WHERE id_siswa='$id_siswa'";
        $db->query($sql);
        
        header('Location: siswa.php');
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container{
            padding-top: 2rem;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php' ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h2>DATA SISWA</h2>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">Tambah Siswa</button>
        </div>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NO</th>
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
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $numb++ . "</td>
                        <td>{$row['nisn']}</td>
                        <td>{$row['nama']}</td>
                            <td>{$row['tgl_lahir']}</td>
                        <td>{$row['alamat']}</td>
                        <td>{$row['ortu']}</td>
                        <td>
                            <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editModal'
                                    data-id='{$row['id_siswa']}' data-nisn='{$row['nisn']}' data-nama='{$row['nama']}'
                                    data-tgl_lahir='{$row['tgl_lahir']}' data-alamat='{$row['alamat']}' data-ortu='{$row['ortu']}'>
                                Edit
                            </button>
                            <button class='btn btn-danger btn-sm' onclick='deleteSiswa({$row['id_siswa']})'>Hapus</button>
                        </td>
                      </tr>";
                } ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="action" value="add">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="action" value="edit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
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