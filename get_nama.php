<?php
include 'debeh.php';
header('Content-Type: application/json'); // Tambahkan header ini
$nisn = $_GET['nisn'];
$result = $db->query("SELECT nama FROM siswa WHERE nisn='$nisn'");
$response = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['status'] = 'success'; // Tambahkan status
    $response['nama'] = $row['nama']; // Tambahkan nama dalam response
} else {
    $response['status'] = 'error'; // Tambahkan status error
    $response['message'] = 'Data Siswa Tidak Valid';
}

echo json_encode($response); // Kembalikan data sebagai JSON
