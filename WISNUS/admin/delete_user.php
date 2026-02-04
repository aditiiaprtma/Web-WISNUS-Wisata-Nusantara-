<?php
session_start();
$file = __DIR__ . "/../data/users.json";

// PROTEKSI: Hanya Admin yang boleh akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak!");
}

if (isset($_GET['id'])) {
    $id_to_delete = $_GET['id'];
    $users = json_decode(file_get_contents($file), true);

    // Filter array: simpan semua user kecuali yang ID-nya cocok dengan $id_to_delete
    $new_users = array_filter($users, function($u) use ($id_to_delete) {
        // Mencegah admin menghapus dirinya sendiri
        return $u['id'] != $id_to_delete; 
    });

    // Simpan kembali ke file JSON
    file_put_contents($file, json_encode(array_values($new_users), JSON_PRETTY_PRINT));

    header("Location: manage_users.php?msg=User berhasil dihapus");
    exit;
}