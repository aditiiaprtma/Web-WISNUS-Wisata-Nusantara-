<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../koneksi.php";

$base_url = "https://adit.skill-issue.space/Project2/";

$query = mysqli_query($conn, "SELECT * FROM wisata");

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $row['gambar'] = $base_url . $row['gambar'];
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
