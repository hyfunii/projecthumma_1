<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../db/debeh.php';

    $nisn = $_POST['nisn'];
    $jarak = $_POST['jarak'];
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
            alert('Siswa sudah terdaftar!');
            window.location.href = '../index.php';
        </script>";
        $db->close();
        exit;
    }

    // Insert data into j_zonasi
    $stmt = $db->prepare("INSERT INTO j_zonasi (nisn, jarak, pilihan) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nisn, $jarak, $pilihan);

    if ($stmt->execute()) {
        // Add to pendaftaran table
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
    $db->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftaran Jalur Zonasi</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.1.1/dist/geosearch.css" />
    <style>
        .container {
            max-width: 800px;
            margin-top: 50px;
        }

        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Data Pendaftaran Jalur Zonasi</h2>
        <form action="j_zonasi.php" method="post">
            <div class="form-group">
                <label for="nisn">NISN</label>
                <input type="number" class="form-control" id="nisn" name="nisn" required>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" readonly>
            </div>
            <div class="form-group">
                <label for="jarak">Jarak (meter)</label>
                <input type="text" class="form-control" id="jarak" name="jarak" readonly>
            </div>
            <div class="form-group">
                <label for="pilihan">Pilihan Jurusan</label>
                <select class="form-control" id="pilihan" name="pilihan" required>
                    <option value="RPL">RPL</option>
                    <option value="DKV">DKV</option>
                    <option value="KKBT">KKBT</option>
                </select>
            </div>
            <div id="map" class="form-group"></div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="../index.php" class="btn btn-secondary">Cancel</a>
            <div class="form-group"></div>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-geosearch@3.1.1/dist/geosearch.umd.js"></script>
    <script>
        var map;
        var marker;
        var lokasiTujuan = [-8.155444866355184, 113.43521678554413];

        function initMap() {
            map = L.map('map').setView(lokasiTujuan, 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            marker = L.marker(lokasiTujuan).addTo(map)
                .bindPopup('Lokasi Tujuan')
                .openPopup();

            map.on('click', function (e) {
                placeMarkerAndCalculateDistance(e.latlng);
            });

            const searchControl = new GeoSearch.GeoSearchControl({
                provider: new GeoSearch.OpenStreetMapProvider(),
                style: 'bar',
                searchLabel: 'Search for a place...',
            });
            map.addControl(searchControl);

            L.control.locate({
                position: 'topright',
                strings: {
                    title: "Show me where I am"
                },
                locateOptions: {
                    maxZoom: 16
                }
            }).addTo(map);
        }

        function placeMarkerAndCalculateDistance(location) {
            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker(location).addTo(map)
                .bindPopup('Lokasi Anda')
                .openPopup();

            var latlng1 = L.latLng(lokasiTujuan);
            var latlng2 = L.latLng(location);
            var distance = latlng1.distanceTo(latlng2);

            document.getElementById('jarak').value = (distance / 1000).toFixed(2); // in kilometers
        }

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

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>

</html>