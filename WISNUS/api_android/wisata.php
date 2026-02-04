<?php
header("Content-Type: application/json");

$file = "../data/wisata.json";
$kategori = $_GET['kategori'] ?? '';

if (!file_exists($file)) {
    echo json_encode([
        "status" => true,
        "data" => []
    ]);
    exit;
}

$data = json_decode(file_get_contents($file), true) ?? [];

// =======================
// FILTER KATEGORI (OPTIONAL)
// =======================
if ($kategori !== '') {
    $data = array_values(array_filter($data, function ($w) use ($kategori) {
        return isset($w['kategori']) &&
               strtolower($w['kategori']) === strtolower($kategori);
    }));
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
