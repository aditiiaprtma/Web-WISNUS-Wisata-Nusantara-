<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// PATH FILE USERS JSON
$file = __DIR__ . "/../data/users.json";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode([
        "status" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

if (!file_exists($file)) {
    echo json_encode([
        "status" => false,
        "message" => "File user tidak ditemukan"
    ]);
    exit;
}

$users = json_decode(file_get_contents($file), true);

foreach ($users as $u) {
    if (
        isset($u['username'], $u['password']) &&
        $u['username'] === $username &&
        password_verify($password, $u['password'])
    ) {
        echo json_encode([
            "status" => true,
            "message" => "Login berhasil",
            "data" => [
                "username" => $u['username'],
                "role" => $u['role'] ?? 'user'
            ]
        ]);
        exit;
    }
}

echo json_encode([
    "status" => false,
    "message" => "Username atau password salah"
]);
