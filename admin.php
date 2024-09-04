<?php
include 'debeh.php';

if (isset($_GET['action']) && $_GET['action'] === 'get_details') {
    $nisn = $_GET['nisn'];
    $sql = "
        SELECT
            s.nisn,
            s.nama AS Nama,
            COALESCE(
                CASE WHEN a.nisn IS NOT NULL THEN 'Jalur Afirmasi'
                     WHEN z.nisn IS NOT NULL THEN 'Jalur Zonasi'
                     WHEN n.nisn IS NOT NULL THEN 'Jalur Nilai Akademik'
                END, 'Tidak Diketahui'
            ) AS Jalur_Pendaftaran,
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
    $stmt->bind_param("s", $nisn);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($data);

    $stmt->close();
    $db->close();
    exit;
}

$orderBy = $_GET['sort'] ?? 's.nisn';
$orderDir = $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';

$query = "
    SELECT
        s.nisn,
        s.nama AS Nama,
        COALESCE(
            CASE WHEN a.nisn IS NOT NULL THEN 'Jalur Afirmasi'
                 WHEN z.nisn IS NOT NULL THEN 'Jalur Zonasi'
                 WHEN n.nisn IS NOT NULL THEN 'Jalur Nilai Akademik'
            END, 'Tidak Diketahui'
        ) AS Jalur_Pendaftaran,
        COALESCE(a.pilihan, z.pilihan, n.pilihan) AS Pilihan
    FROM siswa s
    LEFT JOIN j_afirmasi a ON s.nisn = a.nisn
    LEFT JOIN j_zonasi z ON s.nisn = z.nisn
    LEFT JOIN j_nilai_akademik n ON s.nisn = n.nisn
    WHERE a.nisn IS NOT NULL OR z.nisn IS NOT NULL OR n.nisn IS NOT NULL
    ORDER BY $orderBy $orderDir
";

$result = $db->query($query);
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
        .asc::after { border-bottom: 5px solid #000; }
        .desc::after { border-top: 5px solid #000; }
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
                    echo "<tr>
                        <td>{$numb}</td>
                        <td>{$data_show['nisn']}</td>
                        <td>{$data_show['Nama']}</td>
                        <td>{$data_show['Jalur_Pendaftaran']}</td>
                        <td>{$data_show['Pilihan']}</td>
                        <td><button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#detailModal' data-id='{$data_show['nisn']}'>Detail</button></td>
                    </tr>";
                    $numb++;
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
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
                th.addEventListener('click', () => {
                    const newDir = (currentSort === sortKey && currentDir === 'asc') ? 'desc' : 'asc';
                    urlParams.set('sort', sortKey);
                    urlParams.set('dir', newDir);
                    window.location.search = urlParams.toString();
                });
            });

            document.getElementById('detailModal').addEventListener('show.bs.modal', event => {
                const nisn = event.relatedTarget.getAttribute('data-id');
                fetch(`?action=get_details&nisn=${nisn}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modalContent').innerHTML = `
                            <p><strong>NISN:</strong> ${data.nisn}</p>
                            <p><strong>Nama:</strong> ${data.Nama}</p>
                            <p><strong>Jalur:</strong> ${data.Jalur_Pendaftaran}</p>
                            <p><strong>Pilihan:</strong> ${data.Pilihan}</p>
                            ${data.Doc ? `<p><strong>Doc:</strong> ${data.Doc}</p>` : ''}
                            ${data.Rata_Rata_Nilai ? `<p><strong>Rata Rata Nilai:</strong> ${data.Rata_Rata_Nilai}</p>` : ''}
                            ${data.Jarak_Kesekolah ? `<p><strong>Jarak Kesekolah:</strong> ${data.Jarak_Kesekolah}</p>` : ''}
                        `;
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>
