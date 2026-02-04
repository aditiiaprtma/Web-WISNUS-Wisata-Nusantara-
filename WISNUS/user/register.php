<?php
session_start();
$file = __DIR__ . "/../data/users.json";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_input = trim($_POST['username']);
    $pass_input = $_POST['password'];
    
    // Pastikan folder data dan file ada
    if (!file_exists(__DIR__ . "/../data")) {
        mkdir(__DIR__ . "/../data", 0777, true);
    }

    $users = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    if (!is_array($users)) $users = [];

    // Validasi sederhana
    $error = null;
    if (strlen($user_input) < 4) {
        $error = "Username minimal 4 karakter.";
    } elseif (strlen($pass_input) < 6) {
        $error = "Password minimal 6 karakter.";
    }

    // Cek apakah username sudah ada
    foreach ($users as $u) {
        if (strtolower($u['username']) === strtolower($user_input)) {
            $error = "Username sudah digunakan, cari yang lain!";
            break;
        }
    }

    if (!$error) {
        $newUser = [
            "id" => time(),
            "username" => $user_input,
            "password" => password_hash($pass_input, PASSWORD_DEFAULT), // Keamanan tinggi
            "role" => "user",
            "created_at" => date("Y-m-d H:i:s")
        ];
        
        $users[] = $newUser;
        file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
        
        $success = "Akun berhasil dibuat! Silakan login.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Kontributor | Wisata Nusantara</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .reg-container {
            display: flex; max-width: 850px; width: 100%;
            background: linear-gradient(145deg, #1e293b, #111827);
            border-radius: 24px; overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.05);
        }
        .info-panel {
            flex: 1; padding: 40px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white; display: flex; flex-direction: column; justify-content: center;
        }
        .info-panel h2 { font-size: 26px; margin-bottom: 20px; }
        .feature-list { list-style: none; }
        .feature-list li { margin-bottom: 15px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        
        .form-panel { flex: 1.2; padding: 45px; text-align: left; }
        .form-panel h2 { color: white; margin-bottom: 10px; }
        .form-panel p { color: #94a3b8; font-size: 14px; margin-bottom: 30px; }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; color: #cbd5e1; margin-bottom: 8px; }
        .input-group input {
            width: 100%; padding: 14px; border-radius: 12px;
            background: rgba(15, 23, 42, 0.6); border: 1px solid #334155;
            color: white; font-size: 15px; transition: 0.3s;
        }
        .input-group input:focus { border-color: #6366f1; outline: none; }

        .btn-reg {
            width: 100%; padding: 16px; border: none; border-radius: 12px;
            background: #6366f1; color: white; font-weight: bold;
            cursor: pointer; transition: 0.3s; margin-top: 10px;
        }
        .btn-reg:hover { background: #4f46e5; transform: translateY(-2px); }

        .alert { padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; border: 1px solid; }
        .error { background: rgba(239, 68, 68, 0.1); color: #fca5a5; border-color: rgba(239, 68, 68, 0.2); }
        .success { background: rgba(16, 185, 129, 0.1); color: #a7f3d0; border-color: rgba(16, 185, 129, 0.2); }
        
        .login-link { text-align: center; margin-top: 25px; font-size: 13px; color: #94a3b8; }
        .login-link a { color: #6366f1; text-decoration: none; font-weight: bold; }

        @media (max-width: 700px) {
            .reg-container { flex-direction: column; }
            .info-panel { display: none; }
        }
    </style>
</head>
<body>

<div class="reg-container">
    <div class="info-panel">
        <h2>Ayo Berkontribusi! 🗺️</h2>
        <ul class="feature-list">
            <li>✨ <strong>Profil Kontributor</strong> - Nama Anda tercatat di setiap postingan.</li>
            <li>🚀 <strong>Upload Cepat</strong> - Tanpa harus mengisi ulang data.</li>
            <li>🔒 <strong>Keamanan Data</strong> - Kelola postingan Anda sendiri.</li>
            <li>🌍 <strong>Bantu Wisata Lokal</strong> - Promosikan daerah Anda!</li>
        </ul>
    </div>

    <div class="form-panel">
        <h2>Daftar Akun</h2>
        <p>Bergabunglah dengan komunitas pecinta wisata.</p>

        <?php if(isset($error)): ?> <div class="alert error">⚠️ <?= $error ?></div> <?php endif; ?>
        <?php if(isset($success)): ?> 
            <div class="alert success">✔️ <?= $success ?></div>
            <script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Minimal 4 karakter" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>
            <button type="submit" class="btn-reg">Daftar Akun Kontributor</button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="login.php">Masuk sekarang</a>
        </div>
    </div>
</div>

</body>
</html>