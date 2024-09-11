<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../db/debeh.php';

    $nisn = $_POST['nisn'];
    $pilihan = $_POST['pilihan'];

    if (!isset($_FILES["doc"]) || $_FILES["doc"]["error"] != UPLOAD_ERR_OK) {
        echo "<script>
            alert('No file uploaded or upload error.');
            window.location.href = '../index.php';
        </script>";
        exit;
    }

    $target_dir = "../afirmasidoc/";

    if (!is_dir($target_dir) && !mkdir($target_dir, 0777, true)) {
        echo "<script>
            alert('Failed to create upload directory.');
            window.location.href = '../index.php';
        </script>";
        exit;
    }

    $target_file = $target_dir . basename($_FILES["doc"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


    $check = getimagesize($_FILES["doc"]["tmp_name"]);
    if ($check === false) {
        echo "<script>
            alert('File is not an image.');
            window.location.href = '../index.php';
        </script>";
        $uploadOk = 0;
    }

    if ($_FILES["doc"]["size"] > 5000000) {
        echo "<script>
            alert('Sorry, your file is too large.');
            window.location.href = '../index.php';
        </script>";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "<script>
            alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
            window.location.href = '../index.php';
        </script>";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "<script>
            alert('Sorry, your file was not uploaded.');
            window.location.href = '../index.php';
        </script>";
    } else {
        if (move_uploaded_file($_FILES["doc"]["tmp_name"], $target_file)) {
            $doc = basename($_FILES["doc"]["name"]);

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
                    alert('Siswa sudah terdaftar!');
                    window.location.href = '../index.php';
                </script>";
            } else {

                $stmt = $db->prepare("INSERT INTO j_afirmasi (nisn, doc, pilihan) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $nisn, $doc, $pilihan);

                if ($stmt->execute()) {

                    $stmt = $db->prepare("INSERT INTO pendaftaran (nisn) VALUES (?)");
                    $stmt->bind_param("s", $nisn);
                    $stmt->execute();

                    echo "<script>
                        alert('Data berhasil disimpan!');
                        window.location.href = '../index.php';
                    </script>";
                } else {
                    echo "<script>
                        alert('Gagal menyimpan data.');
                    </script>";
                }

                $stmt->close();
            }
        } else {
            $errorCode = $_FILES["doc"]["error"];
            echo "<script>
                alert('Sorry, there was an error uploading your file. Error code: $errorCode');
                window.location.href = '../index.php';
            </script>";
        }
    }

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
    <div class="container mt-4 md-4">
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
                <label for="doc">Dokumen (gambar)</label>
                <input type="file" class="form-control" id="doc" name="doc" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="pilihan">Pilihan Jurusan</label>
                <select class="form-control" id="pilihan" name="pilihan" required>
                    <option value="RPL">RPL</option>
                    <option value="DKV">DKV</option>
                    <option value="KKBT">KKBT</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Konfirmasi</button>
            <a href="../index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('nisn').addEventListener('input', function () {
            var nisn = this.value;
            if (nisn) {
                fetch('../function/get_nama.php?nisn=' + encodeURIComponent(nisn))
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById('nama').value = data.nama;
                            fetch('../function/get_nilai.php?nisn=' + encodeURIComponent(nisn))
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        document.getElementById('nilai_rata').value = data.nilai_rata;
                                        checkCriteria(data.nilai_rata);
                                    } else {
                                        document.getElementById('nilai_rata').value = '';
                                        document.getElementById('criteria-warning').textContent = data.message;
                                        toggleSubmitButton(false);
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                        } else {
                            document.getElementById('nama').value = '';
                            document.getElementById('nilai_rata').value = '';
                            document.getElementById('criteria-warning').textContent = data.message;
                            toggleSubmitButton(false);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                document.getElementById('nama').value = '';
                document.getElementById('nilai_rata').value = '';
                document.getElementById('criteria-warning').textContent = '';
                toggleSubmitButton(false);
            }
        });
    </script>
</body>

</html>