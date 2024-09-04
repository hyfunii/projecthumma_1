<?php
include 'debeh.php';
$nisn = $_GET['nisn'];
$result = $db->query("SELECT nama FROM siswa WHERE nisn='$nisn'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['nama'];
} else {
    echo "Data Siswa Tidak Valid";
}
