<?php
include '../db/debeh.php';
header('Content-Type: application/json');
$nisn = $_GET['nisn'];
$result = $db->query("SELECT nama FROM siswa WHERE nisn='$nisn'");
$response = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['status'] = 'success';
    $response['nama'] = $row['nama'];
} else {
    $response['status'] = 'error';
    $response['message'] = 'Data Siswa Tidak Valid';
}

echo json_encode($response);
