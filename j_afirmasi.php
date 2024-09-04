<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'debeh.php';

    $nisn = $_POST['nisn'];
    $doc = $_POST['doc'];
    $pilihan = $_POST['pilihan'];

    $tables = ['j_nilai_akademik', 'j_zonasi', 'j_afirmasi'];
    $exists = false;

    foreach ($tables as $table) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE nisn = ?");
        $stmt->bind_param("s", $nisn);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $exists = true;
            break;
        }
    }

    if ($exists) {
        echo "<script>
            alert('NISN sudah ada di salah satu tabel!');
            window.location.href = 'index.php';
        </script>";
        $db->close();
        exit;
    }

    $stmt = $db->prepare("INSERT INTO j_afirmasi (nisn, doc, pilihan) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nisn, $doc, $pilihan);

    if ($stmt->execute()) {
        echo "<script>
            alert('Data berhasil disimpan!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menyimpan data.');
        </script>";
    }

    $stmt->close();
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftaran Jalur Afirmasi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Data Pendaftaran Jalur Afirmasi</h2>
        <form action="j_afirmasi.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nisn">NISN</label>
                <input type="number" class="form-control" id="nisn" name="nisn" required>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" readonly>
            </div>
            <div class="form-group">
                <label for="doc">Dokumen</label>
                <input type="text" class="form-control" id="doc" name="doc" required>
            </div>
            <div class="form-group">
                <label for="pilihan">Pilihan Jurusan</label>
                <select class="form-control" id="pilihan" name="pilihan" required>
                    <option value="RPL">RPL</option>
                    <option value="DKV">DKV</option>
                    <option value="KKBT">KKBT</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('nisn').addEventListener('input', function () {
            var nisn = this.value;
            if (nisn) {
                fetch('get_nama.php?nisn=' + encodeURIComponent(nisn))
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('nama').value = data;
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                document.getElementById('nama').value = '';
            }
        });
    </script>
</body>

</html>