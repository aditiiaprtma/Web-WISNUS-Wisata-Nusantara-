<?php
session_start();

// 1. Proteksi: Hanya user yang sudah login bisa akses
if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
    exit;
}

$username = $_SESSION['user']['username'];
$statusMsg = "";

// 2. Proses saat tombol "Kirim Permintaan" diklik
if (isset($_POST['submit_request'])) {
    $tujuan = trim($_POST['tujuan']);
    $file = 'data/api_requests.json';
    
    // Load data lama atau buat array baru
    $requests = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    // Cek apakah user ini sudah pernah request sebelumnya
    $sudahRequest = false;
    foreach ($requests as $r) {
        if ($r['username'] === $username) { $sudahRequest = true; break; }
    }

    if ($sudahRequest) {
        $statusMsg = "⚠️ Anda sudah mengirim permintaan sebelumnya.";
    } else {
        // Tambahkan data request baru
        $requests[] = [
            "username" => $username,
            "tujuan"   => htmlspecialchars($tujuan),
            "status"   => "pending",
            "api_key"  => null
        ];
        
        file_put_contents($file, json_encode($requests, JSON_PRETTY_PRINT));
        $statusMsg = "✅ Permintaan berhasil dikirim! Silakan tunggu persetujuan Admin.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Request Akses API | Wisata Nusantara</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { background: #0f172a; color: white; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: rgba(30, 41, 59, 0.7); padding: 30px; border-radius: 16px; width: 100%; max-width: 400px; text-align: center; border: 1px solid rgba(255,255,255,0.1); }
        textarea { width: 100%; height: 100px; background: rgba(0,0,0,0.2); border: 1px solid #334155; border-radius: 8px; color: white; padding: 10px; margin: 15px 0; resize: none; }
        .btn { width: 100%; padding: 12px; border-radius: 8px; border: none; background: #0ea5e9; color: white; font-weight: bold; cursor: pointer; }
        .btn:hover { background: #0284c7; }
        .msg { margin-bottom: 15px; font-size: 0.9rem; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Formulir Akses API</h2>
    <p style="font-size: 0.8rem; color: #94a3b8;">Jelaskan tujuan Anda menggunakan API kami:</p>
    
    <?php if($statusMsg): ?>
        <div class="msg"><?= $statusMsg ?></div>
    <?php endif; ?>

    <form method="POST">
        <textarea name="tujuan" placeholder="Contoh: Untuk keperluan tugas sekolah / integrasi website pribadi..." required></textarea>
        <button type="submit" name="submit_request" class="btn">Kirim Permintaan</button>
    </form>
    
    <br>
    <a href="index.php" style="color: #94a3b8; text-decoration: none; font-size: 0.8rem;">Kembali ke Beranda</a>
</div>

</body>
</html>