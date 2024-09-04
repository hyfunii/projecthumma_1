<?php
include 'debeh.php';
header('Content-Type: application/json');
$nisn = $_GET['nisn'];
$stmt = $db->prepare("SELECT nilai_rata FROM nilai WHERE nisn = ?");
$stmt->bind_param("s", $nisn);
$stmt->execute();
$result = $stmt->get_result();

$response = [];
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $response['status'] = 'success';
    $response['nilai_rata'] = $data['nilai_rata'];
} else {
    $response['status'] = 'error';
    $response['message'] = 'Tidak menemukan nilai rata-rata!';
}

echo json_encode($response);

$stmt->close();
$db->close();