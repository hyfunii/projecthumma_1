<?php
include '../db/debeh.php';

header('Content-Type: application/json');

$response = ['success' => false, 'error' => 'Unknown error'];

$action = $_GET['action'] ?? '';
$nisn = $_GET['nisn'] ?? '';


if (!in_array($action, ['lolos', 'tolak']) || !is_numeric($nisn)) {
    $response['error'] = 'Invalid parameters';
    echo json_encode($response);
    exit;
}


error_log("Received action: $action, nisn: $nisn");


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


$db->begin_transaction();

try {

    $query = "INSERT INTO hasil (nisn, jurusan, ket) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('sss', $nisn, $jurusan, $keterangan);
    $stmt->execute();


    $query = "DELETE FROM j_afirmasi WHERE nisn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nisn);
    $stmt->execute();


    $query = "DELETE FROM j_nilai_akademik WHERE nisn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nisn);
    $stmt->execute();


    $query = "DELETE FROM j_zonasi WHERE nisn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nisn);
    $stmt->execute();


    $query = "DELETE FROM pendaftaran WHERE nisn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $nisn);
    $stmt->execute();


    $db->commit();
    $response['success'] = true;
} catch (Exception $e) {

    $db->rollback();
    $response['error'] = 'Database error: ' . $e->getMessage();
}


$stmt->close();
$db->close();


echo json_encode($response);
