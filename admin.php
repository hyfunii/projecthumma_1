<?php
include 'debeh.php';
if (isset($_GET['action']) && $_GET['action'] === 'get_details') {
    $nisn = $_GET['nisn'];
    $sql = "
        SELECT
            s.nisn,
            s.nama AS Nama,
            CASE
                WHEN a.nisn IS NOT NULL THEN 'Jalur Afirmasi'
                WHEN z.nisn IS NOT NULL THEN 'Jalur Zonasi'
                WHEN n.nisn IS NOT NULL THEN 'Jalur Nilai Akademik'
            END AS Jalur_Pendaftaran,
            COALESCE(a.pilihan, z.pilihan, n.pilihan) AS Pilihan,
            a.doc AS Doc,
            n.nilai_rata AS Rata_Rata_Nilai,
            z.jarak AS Jarak_Kesekolah
        FROM siswa s
        LEFT JOIN j_afirmasi a ON s.nisn = a.nisn
        LEFT JOIN j_zonasi z ON s.nisn = z.nisn
        LEFT JOIN j_nilai_akademik n ON s.nisn = n.nisn
        WHERE s.nisn = ?
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $nisn);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($data);

    $stmt->close();
    $db->close();
    exit;
}

$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 's.nisn';
$orderDir = isset($_GET['dir']) && $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';

$show_rel_data = "
    SELECT
        s.nisn,
        s.nama AS Nama,
        CASE
            WHEN a.nisn IS NOT NULL THEN 'Jalur Afirmasi'
            WHEN z.nisn IS NOT NULL THEN 'Jalur Zonasi'
            WHEN n.nisn IS NOT NULL THEN 'Jalur Nilai Akademik'
        END AS Jalur_Pendaftaran,
        COALESCE(a.pilihan, z.pilihan, n.pilihan) AS Pilihan
    FROM siswa s
    LEFT JOIN j_afirmasi a ON s.nisn = a.nisn
    LEFT JOIN j_zonasi z ON s.nisn = z.nisn
    LEFT JOIN j_nilai_akademik n ON s.nisn = n.nisn
    WHERE a.nisn IS NOT NULL
       OR z.nisn IS NOT NULL
       OR n.nisn IS NOT NULL
    ORDER BY $orderBy $orderDir
";

$result = $db->query($show_rel_data);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sortable {
            cursor: pointer;
            position: relative;
        }

        .sortable::after {
            content: ' ';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
        }

        .asc::after {
            border-bottom: 5px solid #000;
        }

        .desc::after {
            border-top: 5px solid #000;
        }
    </style>

</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4">DATA PENDAFTARAN</h2>
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th class="sortable" data-sort="nisn">NISN</th>
                    <th class="sortable" data-sort="nama">Nama</th>
                    <th class="sortable" data-sort="jalur_pendaftaran">Jalur Pendaftaran</th>
                    <th class="sortable" data-sort="pilihan">Pilihan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $numb = 1;
                while ($data_show = $result->fetch_assoc()) {
                    $nisn = $data_show['nisn'];
                    echo "<tr>";
                    echo "<td>" . $numb++ . "</td>";
                    echo "<td>" . ($data_show['nisn']) . "</td>";
                    echo "<td>" . ($data_show['Nama']) . "</td>";
                    echo "<td>" . ($data_show['Jalur_Pendaftaran']) . "</td>";
                    echo "<td>" . ($data_show['Pilihan']) . "</td>";
                    echo "<td><button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#detailModal' data-id='$nisn'>Detail</button></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalContent">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const currentSort = urlParams.get('sort');
            const currentDir = urlParams.get('dir') || 'asc';

            document.querySelectorAll('th[data-sort]').forEach(th => {
                const sortKey = th.getAttribute('data-sort');
                if (sortKey === currentSort) {
                    th.classList.add(currentDir);
                } else {
                    th.classList.remove('asc', 'desc');
                }
            });

            document.querySelectorAll('th[data-sort]').forEach(th => {
                th.addEventListener('click', function () {
                    const sortBy = this.getAttribute('data-sort');
                    const currentUrl = new URL(window.location.href);
                    const newDir = (currentUrl.searchParams.get('sort') === sortBy && currentUrl.searchParams.get('dir') === 'asc') ? 'desc' : 'asc';

                    currentUrl.searchParams.set('sort', sortBy);
                    currentUrl.searchParams.set('dir', newDir);
                    window.location.href = currentUrl.toString();
                });
            });

            var detailModal = document.getElementById('detailModal');
            detailModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var nisn = button.getAttribute('data-id');

                fetch('?action=get_details&nisn=' + nisn)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        var modalContent = document.getElementById('modalContent');
                        var content = '<p><strong>NISN:</strong> ' + data.nisn + '</p>' +
                            '<p><strong>Nama:</strong> ' + data.Nama + '</p>' +
                            '<p><strong>Pilihan:</strong> ' + data.Pilihan + '</p>' +
                            '<p><strong>Jalur:</strong> ' + data.Jalur_Pendaftaran + '</p>';
                        if (data.Doc !== undefined && data.Doc !== null) {
                            content += '<p><strong>Doc:</strong> ' + data.Doc + '</p>';
                        } else if (data.Rata_Rata_Nilai !== undefined && data.Rata_Rata_Nilai !== null) {
                            content += '<p><strong>Rata Rata Nilai:</strong> ' + data.Rata_Rata_Nilai + '</p>';
                        } else if (data.Jarak_Kesekolah !== undefined && data.Jarak_Kesekolah !== null) {
                            content += '<p><strong>Jarak Kesekolah:</strong> ' + data.Jarak_Kesekolah + '</p>';
                        }
                        modalContent.innerHTML = content;
                    })
                    .catch(error => console.error('Error fetching data:', error));
            });
        });
    </script>
</body>

</html>