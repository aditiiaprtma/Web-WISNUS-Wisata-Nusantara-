<?php
header("Content-Type: application/json");

$targetDir = "../uploads/";
$dataFile  = "../data/wisata_pending.json";

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$nama      = trim($_POST['nama'] ?? '');
$daerah    = trim($_POST['daerah'] ?? '');
$kategori  = $_POST['kategori'] ?? '';
$deskripsi = trim($_POST['deskripsi'] ?? '');
$username  = $_POST['username'] ?? 'android_user';

if (empty($nama) || empty($daerah) || empty($deskripsi)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit;
}

// =====================
// HANDLE GAMBAR BASE64
// =====================
$gambarPath = "uploads/default.jpg";

if (!empty($_POST['gambar_base64'])) {

    $imgData = base64_decode($_POST['gambar_base64']);
    if ($imgData !== false) {
        $fileName = time() . "_android.jpg";
        file_put_contents($targetDir . $fileName, $imgData);
        $gambarPath = "uploads/" . $fileName;
    }
}

// =====================
// SIMPAN DATA
// =====================
$currentData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
if (!is_array($currentData)) $currentData = [];

$currentData[] = [
    "id" => time(),
    "nama" => $nama,
    "daerah" => $daerah,
    "kategori" => $kategori,
    "deskripsi" => $deskripsi,
    "gambar" => $gambarPath,
    "user" => $username,
    "role_user" => "android",
    "status" => "pending",
    "tanggal" => date("Y-m-d H:i:s")
];

file_put_contents($dataFile, json_encode($currentData, JSON_PRETTY_PRINT));

echo json_encode(["status" => "success", "message" => "Data berhasil dikirim"]);
