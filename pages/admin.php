<?php
include '../db/debeh.php';

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
            CONCAT('../afirmasidoc/', a.doc) AS DocURL,
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

$rows = [];
while ($data_show = $result->fetch_assoc()) {
    $rows[] = $data_show;
}
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

        .asc::after {
            border-bottom: 5px solid #000;
        }

        .desc::after {
            border-top: 5px solid #000;
        }

        .container {
            padding-top: 2rem;
        }
    </style>
</head>

<body>
    <?php include '../navbaradmin.php'; ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Data Pendaftaran</h2>
        </div>
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th class="sortable" data-sort="nisn">NISN</th>
                    <th class="sortable" data-sort="nama">Nama</th>
                    <th class="sortable" data-sort="jalur_pendaftaran">Jalur Pendaftaran</th>
                    <th class="sortable" data-sort="pilihan">Pilihan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php else: ?>
                    <?php $numb = 1; ?>
                    <?php foreach ($rows as $data_show): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($numb++); ?></td>
                            <td><?php echo htmlspecialchars($data_show['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($data_show['Nama']); ?></td>
                            <td><?php echo htmlspecialchars($data_show['Jalur_Pendaftaran']); ?></td>
                            <td><?php echo htmlspecialchars($data_show['Pilihan']); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal"
                                    data-id="<?php echo htmlspecialchars($data_show['nisn']); ?>">Detail</button>
                                <button class="btn btn-success btn-sm btn-lolos"
                                    data-id="<?php echo htmlspecialchars($data_show['nisn']); ?>">Lolos</button>
                                <button class="btn btn-danger btn-sm btn-tolak"
                                    data-id="<?php echo htmlspecialchars($data_show['nisn']); ?>">Tolak</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
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

            const detailModal = document.getElementById('detailModal');
            detailModal.addEventListener('show.bs.modal', event => {
                const nisn = event.relatedTarget.getAttribute('data-id');
                fetch(`?action=get_details&nisn=${nisn}`)
                    .then(response => response.json())
                    .then(data => {
                        let docContent = '';
                        if (data.DocURL) {
                            docContent = `
                        <p><strong>Doc:</strong> 
                            <a href="${data.DocURL}" target="_blank" class="btn btn-info btn-sm">View</a>
                            <button class="btn btn-secondary btn-sm" id="fullscreenBtn" data-img-url="${data.DocURL}">Fullscreen</button>
                            <a href="${data.DocURL}" download class="btn btn-primary btn-sm">Download</a>
                        </p>
                        <img src="${data.DocURL}" alt="Document" style="max-width: 100%; height: auto; display: block;">
                    `;
                        }
                        document.getElementById('modalContent').innerHTML = `
                    <p><strong>NISN:</strong> ${data.nisn}</p>
                    <p><strong>Nama:</strong> ${data.Nama}</p>
                    <p><strong>Jalur:</strong> ${data.Jalur_Pendaftaran}</p>
                    <p><strong>Pilihan:</strong> ${data.Pilihan}</p>
                    ${docContent}
                    ${data.Rata_Rata_Nilai ? `<p><strong>Rata Rata Nilai:</strong> ${data.Rata_Rata_Nilai}</p>` : ''}
                    ${data.Jarak_Kesekolah ? `<p><strong>Jarak Kesekolah:</strong> ${data.Jarak_Kesekolah} Km</p>` : ''}
                    <br>
                    <button class="btn btn-success btn-action" data-action="lolos" data-id="${data.nisn}">Lolos</button>
                    <button class="btn btn-danger btn-action" data-action="tolak" data-id="${data.nisn}">Tolak</button>
                `;
                    })
                    .catch(error => console.error('Error:', error));
            });

            document.addEventListener('click', event => {
                if (event.target.id === 'fullscreenBtn') {
                    const imgUrl = event.target.getAttribute('data-img-url');
                    const imgWindow = window.open(imgUrl, '_blank');
                    imgWindow.focus();
                }

                if (event.target.classList.contains('btn-action')) {
                    const action = event.target.getAttribute('data-action');
                    const nisn = event.target.getAttribute('data-id');
                    console.log(`Sending request: action=${action}, nisn=${nisn}`);
                    fetch(`../function/handle_action.php?action=${action}&nisn=${nisn}`, {
                        method: 'GET'
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Data berhasil diproses.');
                                window.location.reload();
                            } else {
                                alert(`Terjadi kesalahan: ${data.error}`);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            document.querySelectorAll('.btn-lolos, .btn-tolak').forEach(button => {
                button.addEventListener('click', () => {
                    const nisn = button.getAttribute('data-id');
                    const action = button.classList.contains('btn-lolos') ? 'lolos' : 'tolak';
                    console.log(`Sending request: action=${action}, nisn=${nisn}`);
                    fetch(`../function/handle_action.php?action=${action}&nisn=${nisn}`, {
                        method: 'GET'
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Data berhasil diproses.');
                                window.location.reload();
                            } else {
                                alert(`Terjadi kesalahan: ${data.error}`);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</body>

</html>