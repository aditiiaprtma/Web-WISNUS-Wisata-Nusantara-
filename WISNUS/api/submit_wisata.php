<?php
session_start();
header("Content-Type: application/json");

/* =====================
   1. PROTEKSI LAYER 1 (Login & Role)
===================== */

// Pastikan user sudah login
if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Sesi habis, silakan login kembali."]);
    exit;
}

// KHUSUS: Tolak kiriman jika role adalah Guest (Tamu)
if ($_SESSION['user']['role'] === 'guest') {
    echo json_encode([
        "status" => "error", 
        "message" => "Akses Ditolak! Tamu hanya diizinkan melihat-lihat. Silakan daftar akun untuk berkontribusi."
    ]);
    exit;
}

/* =====================
   2. DATA & PATH CONFIG
===================== */
$targetDir = "../uploads/";
$dataFile  = "../data/wisata_pending.json";

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Ambil input dari form
$nama      = trim($_POST['nama'] ?? '');
$daerah    = trim($_POST['daerah'] ?? '');
$kategori  = $_POST['kategori'] ?? '';
$deskripsi = trim($_POST['deskripsi'] ?? '');

/* =====================
   3. VALIDASI DATA
===================== */
if (empty($nama) || empty($daerah) || empty($deskripsi) || !isset($_FILES['gambar'])) {
    echo json_encode(["status" => "error", "message" => "Mohon lengkapi semua data form!"]);
    exit;
}

// Olah file gambar
$fileInfo = pathinfo($_FILES['gambar']['name']);
$ext = strtolower($fileInfo['extension']);
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($ext, $allowedExtensions)) {
    echo json_encode(["status" => "error", "message" => "Format gambar ditolak! Gunakan JPG, PNG, atau WEBP."]);
    exit;
}

// Buat nama file unik (Pembersihan nama file agar tidak ada karakter aneh)
$safeName = preg_replace("/[^a-zA-Z0-9]/", "", $nama);
$fileName = time() . "_" . $safeName . "." . $ext;
$targetFilePath = $targetDir . $fileName;

/* =====================
   4. EKSEKUSI UPLOAD & SIMPAN
===================== */
if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFilePath)) {
    
    // Baca data yang sudah ada
    $currentData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
    if (!is_array($currentData)) $currentData = [];
    
    // Data baru
    $newData = [
        "id"        => time(),
        "nama"      => $nama,
        "daerah"    => $daerah,
        "kategori"  => $kategori,
        "deskripsi" => $deskripsi,
        "gambar"    => "uploads/" . $fileName, 
        "user"      => $_SESSION['user']['username'],
        "role_user" => $_SESSION['user']['role'],
        "status"    => "pending",
        "tanggal"   => date("Y-m-d H:i:s")
    ];
    
    $currentData[] = $newData;
    
    // Simpan ke wisata_pending.json dengan penguncian file (LOCK_EX)
    if (file_put_contents($dataFile, json_encode($currentData, JSON_PRETTY_PRINT), LOCK_EX)) {
        echo json_encode(["status" => "success", "message" => "Berhasil! Data Anda masuk antrean review admin."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database sibuk, gagal menyimpan data."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Gagal mengunggah gambar ke folder server."]);
}