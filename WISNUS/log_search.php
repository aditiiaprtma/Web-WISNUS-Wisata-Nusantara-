<?php
/**
 * log_search.php
 * Mencatat pencarian user (lokasi / wisata)
 * Digunakan oleh Web & API
 */

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// =====================
// KONFIGURASI
// =====================
$logDir  = __DIR__ . "/data";
$logFile = $logDir . "/log.txt";

// =====================
// VALIDASI METHOD
// =====================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

// =====================
// AMBIL DATA
// =====================
$keyword = trim($_POST['title'] ?? '');

if ($keyword === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Keyword tidak boleh kosong"
    ]);
    exit;
}

// =====================
// BUAT FOLDER JIKA BELUM ADA
// =====================
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// =====================
// SIMPAN LOG
// =====================
$time = date("Y-m-d H:i:s");
$line = $time . " | " . $keyword . PHP_EOL;

file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

// =====================
// RESPONSE
// =====================
echo json_encode([
    "status"  => "success",
    "message" => "Log berhasil disimpan",
    "data"    => [
        "keyword" => $keyword,
        "time"    => $time
    ]
]);
