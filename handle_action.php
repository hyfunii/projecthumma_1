<?php
include 'debeh.php'; // Pastikan ini menginisialisasi koneksi ke database

header('Content-Type: application/json');

$response = ['success' => false, 'error' => 'Unknown error'];

$action = $_GET['action'] ?? '';
$nisn = $_GET['nisn'] ?? '';

// Validasi input
if (!in_array($action, ['lolos', 'tolak']) || !is_numeric($nisn)) {
    $response['error'] = 'Invalid parameters';
    echo json_encode($response);
    exit;
}

// Debugging
error_log("Received action: $action, nisn: $nisn");

// Ambil pilihan dari tabel pendaftaran
$query = "SELECT
            COALESCE(a.pilihan, z.pilihan, n.pilihan) AS Pilihan
          FROM pendaftaran p
          LEFT JOIN j_afirmasi a ON p.nisn = a.nisn
          LEFT JOIN j_zonasi z ON p.nisn = z.nisn
          LEFT JOIN j_nilai_akademik n ON p.nisn = n.nisn
          WHERE p.nisn = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('s', $nisn);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    $response['error'] = 'NISN not found';
    echo json_encode($response);
    exit;
}

$jurusan = $data['Pilihan'] ?? '';
$keterangan = ($action === 'lolos') ? 'Lolos' : 'Tolak';

// Mulai transaksi
$db->begin_transaction();

try {
    // Masukkan data ke tabel hasil
    $query = "INSERT INTO hasil (nisn, jurusan, ket) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('sss', $nisn, $jurusan, $keterangan);
    $stmt->execute();

    // Hapus data dari tabel j_afirmasi
    $query = "DELETE FROM j_afirmasi WHERE nisn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nisn);
    $stmt->execute();

    // Hapus data dari tabel j_nilai_akademik
    $query = "DELETE FROM j_nilai_akademik WHERE nisn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nisn);
    $stmt->execute();

    // Hapus data dari tabel j_zonasi
    $query = "DELETE FROM j_zonasi WHERE nisn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nisn);
    $stmt->execute();

    // Hapus data dari tabel pendaftaran
    $query = "DELETE FROM pendaftaran WHERE nisn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nisn);
    $stmt->execute();

    // Commit transaksi
    $db->commit();
    $response['success'] = true;
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $db->rollback();
    $response['error'] = 'Database error: ' . $e->getMessage();
}

// Tutup koneksi
$stmt->close();
$db->close();

// Kirimkan respons JSON
echo json_encode($response);
