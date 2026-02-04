<?php
header("Content-Type: application/json");

$dataFile = "../data/users.json";

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo json_encode([
        "status" => false,
        "message" => "Data login tidak lengkap"
    ]);
    exit;
}

$users = file_exists($dataFile)
    ? json_decode(file_get_contents($dataFile), true)
    : [];

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
                "id" => $u['id'] ?? null,
                "username" => $u['username'],
                "role" => $u['role'] ?? "user",
                "api_key" => $u['api_key'] ?? null
            ]
        ]);
        exit;
    }
}

echo json_encode([
    "status" => false,
    "message" => "Username atau password salah"
]);
