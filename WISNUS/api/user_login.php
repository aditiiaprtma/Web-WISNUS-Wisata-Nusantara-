<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$usersFile = __DIR__ . "/../data/users.json";

$email = $_POST['email'] ?? '';
$pass  = $_POST['password'] ?? '';

if (!$email || !$pass) {
    echo json_encode(["status"=>"error","message"=>"Data tidak lengkap"]);
    exit;
}

$users = json_decode(file_get_contents($usersFile), true);

foreach ($users as $u) {
    if ($u['email'] === $email && $u['password'] === $pass) {

        $token = bin2hex(random_bytes(16));
        $expire = time() + (60 * 10); // 10 menit

        file_put_contents(
            __DIR__ . "/../data/token.json",
            json_encode([
                "token" => $token,
                "expire" => $expire,
                "email" => $email
            ])
        );

        echo json_encode([
            "status"=>"success",
            "token"=>$token,
            "expired"=>"10 menit"
        ]);
        exit;
    }
}

echo json_encode(["status"=>"error","message"=>"Login gagal"]);
