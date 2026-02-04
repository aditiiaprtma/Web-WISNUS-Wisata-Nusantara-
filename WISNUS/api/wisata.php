<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// path ke file json
$file = __DIR__ . "/../data/wisata.json";

if (!file_exists($file)) {
    echo json_encode([
        "status" => false,
        "message" => "File JSON tidak ditemukan"
    ]);
    exit;
}

$data = json_decode(file_get_contents($file), true);

echo json_encode([
    "status" => true,
    "data" => $data
]);
