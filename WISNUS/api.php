<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

/* =====================
   1. PATH DATA
===================== */
$activeFile = __DIR__ . "/data/wisata.json";
$apiReqFile = __DIR__ . "/data/api_requests.json";

/* =====================
   2. CEK OTORISASI (BYPASS ADMIN)
===================== */
$hasAccess = false;
$errorMsg = "Akses Ditolak. API Key tidak valid.";

// A. Cek apakah yang akses adalah Admin yang sedang login
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    $hasAccess = true;
} else {
    // B. Cek API Key dari URL (?key=WST-xxx)
    $providedKey = $_GET['key'] ?? '';
    
    if (!empty($providedKey) && file_exists($apiReqFile)) {
        $apiRequests = json_decode(file_get_contents($apiReqFile), true) ?: [];
        
        foreach ($apiRequests as $req) {
            if ($req['api_key'] === $providedKey) {
                // Pastikan statusnya APPROVED
                if ($req['status'] === 'approved') {
                    $hasAccess = true;
                } else if ($req['status'] === 'revoked') {
                    // Jika status dicabut oleh Admin
                    $errorMsg = "Akses Ditolak. API Key ini telah dimatikan/dicabut oleh Admin.";
                }
                break;
            }
        }
    }
}

// Jika tidak punya akses
if (!$hasAccess) {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "message" => $errorMsg
    ], JSON_PRETTY_PRINT);
    exit;
}

/* =====================
   3. AMBIL & FILTER DATA
===================== */
if (!file_exists($activeFile)) {
    echo json_encode(["status" => "success", "data" => []]);
    exit;
}

$dataWisata = json_decode(file_get_contents($activeFile), true) ?: [];

// Fitur: Lihat data spesifik berdasarkan ID (?id=1)
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $filtered = array_filter($dataWisata, fn($w) => (int)$w['id'] === $id);
    $result = array_values($filtered);
    
    echo json_encode([
        "status" => "success",
        "total" => count($result),
        "data" => $result
    ], JSON_PRETTY_PRINT);
} else {
    // Tampilkan semua data
    echo json_encode([
        "status" => "success",
        "total" => count($dataWisata),
        "data" => $dataWisata
    ], JSON_PRETTY_PRINT);
}