<?php
header("Content-Type: application/json; charset=UTF-8");

// AMBIL DATA POST
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username == '' || $password == '') {
    echo json_encode([
        "status" => "error",
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

// FILE DATA JSON
$file = "../data/users.json";
$users = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

// CEK USERNAME SUDAH ADA
foreach ($users as $u) {
    if ($u['username'] === $username) {
        echo json_encode([
            "status" => "error",
            "message" => "Username sudah terdaftar"
        ]);
        exit;
    }
}

// TAMBAH USER BARU
$users[] = [
    "username" => $username,
    "password" => password_hash($password, PASSWORD_DEFAULT),
    "role" => "user"
];

// SIMPAN KE JSON
file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

// RESPONSE SUKSES
echo json_encode([
    "status" => "success",
    "message" => "Register berhasil"
]);
