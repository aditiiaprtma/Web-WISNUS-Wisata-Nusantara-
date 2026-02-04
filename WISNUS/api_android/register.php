<?php
header("Content-Type: application/json");

$dataFile = "../data/users.json";

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo json_encode([
        "status" => false,
        "message" => "Username dan password wajib diisi"
    ]);
    exit;
}

$users = file_exists($dataFile)
    ? json_decode(file_get_contents($dataFile), true)
    : [];

foreach ($users as $u) {
    if ($u['username'] === $username) {
        echo json_encode([
            "status" => false,
            "message" => "Username sudah terdaftar"
        ]);
        exit;
    }
}

$users[] = [
    "id" => time(),
    "username" => $username,
    "password" => password_hash($password, PASSWORD_DEFAULT)
];

file_put_contents($dataFile, json_encode($users, JSON_PRETTY_PRINT));

echo json_encode([
    "status" => true,
    "message" => "Register berhasil"
]);
