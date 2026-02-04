<?php
session_start();

// 1. Cek Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak");
}

// 2. Cek ID
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$idToDelete = $_GET['id'];
$jsonFile = __DIR__ . "/../data/wisata.json";

// 3. Load Data
$data = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
$newData = [];
$deleted = false;

// 4. Filter Data
foreach ($data as $item) {
    if ($item['id'] == $idToDelete) {
        // Hapus file gambar dari server agar hemat storage
        if (!empty($item['gambar'])) {
            $filePath = __DIR__ . "/../" . $item['gambar'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $deleted = true;
        continue; // Jangan masukkan ke newData (skip = hapus)
    }
    $newData[] = $item;
}

// 5. Simpan Kembali
if ($deleted) {
    file_put_contents($jsonFile, json_encode($newData, JSON_PRETTY_PRINT));
}

// 6. Kembali ke Dashboard
header("Location: dashboard.php");
exit;