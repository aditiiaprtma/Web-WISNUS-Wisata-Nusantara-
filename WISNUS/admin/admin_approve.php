<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");



/* =====================

   1. PROTEKSI ADMIN

===================== */

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {

    http_response_code(403);

    echo json_encode(["status" => "error", "message" => "Akses ditolak"]);

    exit;

}



/* =====================

   2. KONFIGURASI FILE

===================== */

$pendingFile = __DIR__ . "/../data/wisata_pending.json";

$mainFile    = __DIR__ . "/../data/wisata.json";



// Mengambil data dari POST

$id     = $_POST['id'] ?? '';

$action = $_POST['action'] ?? ''; // 'approve', 'reject', atau 'delete'



if (!$id || !in_array($action, ['approve', 'reject', 'delete'])) {

    echo json_encode([

        "status" => "error", 

        "message" => "Parameter tidak valid. Diterima: ID=$id, Action=$action"

    ]);

    exit;

}



/* =====================

   3. LOAD DATA

===================== */

$pending = file_exists($pendingFile) ? json_decode(file_get_contents($pendingFile), true) : [];

$main    = file_exists($mainFile) ? json_decode(file_get_contents($mainFile), true) : [];



if (!is_array($pending)) $pending = [];

if (!is_array($main)) $main = [];



$found = false;



/* =====================

   4. LOGIKA PROSES

===================== */



// A. JIKA ACTION ADALAH APPROVE ATAU REJECT (DARI PENDING)

if ($action === 'approve' || $action === 'reject') {

    $newPending = [];

    foreach ($pending as $w) {

        if ((string)$w['id'] === (string)$id) {

            $found = true;

            if ($action === "approve") {

                if (isset($w['status'])) unset($w['status']);

                $main[] = $w;

            } else {

                // Hapus gambar jika reject

                if (!empty($w['gambar'])) {

                    $path = __DIR__ . "/../" . $w['gambar'];

                    if (file_exists($path)) @unlink($path);

                }

            }

            continue; // Hapus dari antrean

        }

        $newPending[] = $w;

    }

    if ($found) {

        file_put_contents($pendingFile, json_encode(array_values($newPending), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

        file_put_contents($mainFile, json_encode(array_values($main), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

    }

} 



// B. JIKA ACTION ADALAH DELETE (DARI WISATA AKTIF)

else if ($action === 'delete') {

    $newMain = [];

    foreach ($main as $w) {

        if ((string)$w['id'] === (string)$id) {

            $found = true;

            // Hapus gambar fisik

            if (!empty($w['gambar'])) {

                $path = __DIR__ . "/../" . $w['gambar'];

                if (file_exists($path)) @unlink($path);

            }

            continue; // Hapus dari data aktif

        }

        $newMain[] = $w;

    }

    if ($found) {

        file_put_contents($mainFile, json_encode(array_values($newMain), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

    }

}



/* =====================

   5. RESPON AKHIR

===================== */

if ($found) {

    echo json_encode(["status" => "success", "message" => "Berhasil memproses $action"]);

} else {

    echo json_encode(["status" => "error", "message" => "Data dengan ID $id tidak ditemukan di database."]);

}