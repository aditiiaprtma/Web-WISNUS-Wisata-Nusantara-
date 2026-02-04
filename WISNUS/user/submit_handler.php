<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../login.php"); exit; }

// Konfigurasi
$uploadDir = "../uploads/";
$pendingFile = "../data/wisata_pending.json";

// Buat folder jika belum ada
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Proses Upload Gambar
$gambarPath = "";
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid("img_") . "." . $ext;
    
    // Simpan ke folder uploads
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename)) {
        $gambarPath = "uploads/" . $filename; // Path relatif untuk disimpan di JSON
    }
}

// Data Baru
$newData = [
    "id" => time(),
    "nama" => $_POST['nama'],
    "daerah" => $_POST['daerah'],
    "kategori" => $_POST['kategori'],
    "deskripsi" => $_POST['deskripsi'],
    "gambar" => $gambarPath,
    "status" => "pending",
    "user" => $_SESSION['user']['username']
];

// Simpan ke JSON Pending
$currentData = file_exists($pendingFile) ? json_decode(file_get_contents($pendingFile), true) : [];
if (!is_array($currentData)) $currentData = [];

$currentData[] = $newData;
file_put_contents($pendingFile, json_encode($currentData, JSON_PRETTY_PRINT));

// Redirect dengan sukses
echo "<script>alert('Berhasil dikirim! Menunggu persetujuan admin.'); window.location.href='submit.php';</script>";
?>