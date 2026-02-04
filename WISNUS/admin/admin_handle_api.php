\<?php
session_start();
header("Content-Type: application/json");

/* =====================
   1. PROTEKSI ADMIN
===================== */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Akses ditolak. Anda bukan admin."]);
    exit;
}

/* =====================
   2. VALIDASI DATA
===================== */
$file = '../data/api_requests.json';
$username = $_POST['username'] ?? '';
$action = $_POST['action'] ?? ''; // 'approve' atau 'revoke'

if (empty($username) || empty($action)) {
    echo json_encode(["status" => "error", "message" => "Parameter tidak lengkap."]);
    exit;
}

/* =====================
   3. PROSES DATA
===================== */
if (file_exists($file)) {
    $requests = json_decode(file_get_contents($file), true) ?: [];
    $found = false;

    foreach ($requests as &$req) {
        if ($req['username'] === $username) {
            if ($action === 'approve') {
                // Logika: Buat kunci baru HANYA jika status sebelumnya pending.
                // Jika sebelumnya 'revoked', kita hanya mengaktifkan kembali yang lama.
                if ($req['status'] === 'pending' || empty($req['api_key'])) {
                    $req['api_key'] = "WST-" . strtoupper(bin2hex(random_bytes(4)));
                }
                $req['status'] = 'approved';
                $message = "API Key untuk $username berhasil diaktifkan.";
            } else {
                // Mengubah status menjadi revoked
                $req['status'] = 'revoked';
                $message = "API Key untuk $username telah dicabut/dimatikan.";
            }
            $found = true;
            break;
        }
    }

    if ($found) {
        file_put_contents($file, json_encode($requests, JSON_PRETTY_PRINT));
        echo json_encode(["status" => "success", "message" => $message]);
    } else {
        echo json_encode(["status" => "error", "message" => "User tidak ditemukan dalam antrean API."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Database API tidak ditemukan."]);
}
?>