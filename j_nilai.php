<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'debeh.php';

    $nisn = $_POST['nisn'];
    $nilai_rata = $_POST['nilai_rata'];
    $pilihan = $_POST['pilihan'];

    if (empty($nisn) || empty($nilai_rata) || empty($pilihan)) {
        die('Error: Data tidak valid.');
    }

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
            window.location.href = 'index.php';
        </script>";
        $db->close();
        exit;
    }

    $kriteria = [
        'RPL' => 79.00,
        'DKV' => 75.00,
        'KKBT' => 82.00
    ];

    if ($nilai_rata < $kriteria[$pilihan]) {
        echo "<script>
            alert('Anda tidak memiliki kriteria yang cukup untuk masuk ke jurusan ini!');
        </script>";
    } else {
        // Insert data into j_nilai_akademik
        $stmt = $db->prepare("INSERT INTO j_nilai_akademik (nisn, nilai_rata, pilihan) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die('Error: Gagal mempersiapkan statement.');
        }

        $stmt->bind_param("sss", $nisn, $nilai_rata, $pilihan);

        if ($stmt->execute()) {
            // Add to pendaftaran table
            $stmt = $db->prepare("INSERT INTO pendaftaran (nisn) VALUES (?)");
            $stmt->bind_param("s", $nisn);
            $stmt->execute();

            echo "<script>
                alert('Data berhasil disimpan!');
                window.location.href = 'index.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menyimpan data: " . $stmt->error . "');
            </script>";
        }

        $stmt->close();
    }

    $db->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftaran Nilai Akademik</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }

        .warning {
            color: red;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Data Pendaftaran Nilai Akademik</h2>
        <form action="j_nilai.php" method="post">
            <div class="form-group">
                <label for="nisn">NISN</label>
                <input type="number" class="form-control" id="nisn" name="nisn" required>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" readonly>
            </div>
            <div class="form-group">
                <label for="nilai_rata">Nilai Rata Rata</label>
                <input type="text" class="form-control" id="nilai_rata" name="nilai_rata" readonly>
            </div>
            <div class="form-group">
                <label for="pilihan">Pilihan Jurusan</label>
                <select class="form-control" id="pilihan" name="pilihan" required>
                    <option value="RPL">RPL</option>
                    <option value="DKV">DKV</option>
                    <option value="KKBT">KKBT</option>
                </select>
                <small id="criteria-warning" class="form-text warning"></small>
            </div>
            <button type="submit" class="btn btn-primary" id="submit-btn">Submit</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        document.getElementById('nisn').addEventListener('input', function () {
            var nisn = this.value;
            if (nisn) {
                fetch('get_nama.php?nisn=' + encodeURIComponent(nisn))
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById('nama').value = data.nama;
                            fetch('get_nilai.php?nisn=' + encodeURIComponent(nisn))
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

        function checkCriteria(nilai_rata) {
            var pilihan = document.getElementById('pilihan').value;
            var kriteria = {
                'RPL': 79.00,
                'DKV': 75.00,
                'KKBT': 82.00
            };
            if (nilai_rata < kriteria[pilihan]) {
                document.getElementById('criteria-warning').textContent = 'Anda tidak memiliki kriteria yang cukup untuk masuk ke jurusan ini!';
                toggleSubmitButton(false);
            } else {
                document.getElementById('criteria-warning').textContent = '';
                toggleSubmitButton(true);
            }
        }

        document.getElementById('pilihan').addEventListener('change', function () {
            var nilai_rata = document.getElementById('nilai_rata').value;
            checkCriteria(nilai_rata);
        });

        function toggleSubmitButton(enabled) {
            var submitButton = document.getElementById('submit-btn');
            submitButton.disabled = !enabled;
        }
    </script>

</body>

</html>