<?php
session_start();

// Path ke file database
$file = __DIR__ . "/../data/users.json";

// 1. LOGIKA MASUK SEBAGAI TAMU (GUEST)
if (isset($_GET['action']) && $_GET['action'] === 'guest') {
    $_SESSION['user'] = [
        "id"       => "guest_" . time(),
        "username" => "Tamu_" . substr(uniqid(), -4),
        "role"     => "guest"
    ];
    header("Location: ../index.php");
    exit;
}

// 2. LOGIKA LOGIN MEMBER & ADMIN
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!file_exists($file)) {
        $error = "Database user tidak ditemukan.";
    } else {
        $users = json_decode(file_get_contents($file), true);
        $user_input = trim($_POST['username']);
        $pass_input = trim($_POST['password']);
        $found = false;

        foreach ($users as $u) {
            if ($u['username'] === $user_input && password_verify($pass_input, $u['password'])) {
                $_SESSION['user'] = [
                    "id"       => $u['id'],
                    "username" => $u['username'],
                    "role"     => $u['role']
                ];
                $found = true;
                
                // Redirect berdasarkan role
                if ($u['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../index.php");
                }
                exit;
            }
        }
        if (!$found) $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Wisata Nusantara</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            display: flex; align-items: center; justify-content: center; padding: 20px;
            color: #f8fafc;
        }
        .login-card {
            background: linear-gradient(145deg, #1e293b, #111827);
            width: 100%; max-width: 400px; padding: 40px; border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .logo { font-size: 50px; margin-bottom: 10px; }
        h2 { margin-bottom: 8px; font-weight: 700; }
        .subtitle { color: #94a3b8; font-size: 14px; margin-bottom: 25px; }
        
        /* Form Styling */
        .input-group { margin-bottom: 20px; text-align: left; position: relative; }
        .input-group label { display: block; font-size: 13px; color: #cbd5e1; margin-bottom: 8px; }
        .input-group input {
            width: 100%; padding: 14px; border-radius: 12px;
            background: rgba(15, 23, 42, 0.6); border: 1px solid #334155;
            color: white; font-size: 15px; transition: 0.3s;
        }
        .input-group input:focus { outline: none; border-color: #6366f1; }
        
        button {
            width: 100%; padding: 16px; border: none; border-radius: 12px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        button:hover { transform: translateY(-2px); filter: brightness(1.1); }

        /* Guest & Register Styling */
        .divider { margin: 25px 0; border-top: 1px solid rgba(255,255,255,0.1); position: relative; }
        .divider span { position: absolute; top: -10px; left: 50%; transform: translateX(-50%); 
                        background: #151d2e; padding: 0 10px; color: #475569; font-size: 12px; }
        
        .btn-guest {
            display: block; width: 100%; padding: 12px; border: 1px dashed #6366f1;
            border-radius: 12px; color: #818cf8; text-decoration: none;
            font-size: 14px; font-weight: 600; transition: 0.3s; margin-bottom: 20px;
        }
        .btn-guest:hover { background: rgba(99, 102, 241, 0.05); color: white; }

        .register-link { font-size: 14px; color: #94a3b8; text-decoration: none; }
        .register-link strong { color: #6366f1; }

        .alert { background: rgba(239, 68, 68, 0.1); color: #fca5a5; padding: 12px; 
                 border-radius: 12px; margin-bottom: 20px; font-size: 13px; border: 1px solid rgba(239, 68, 68, 0.2); }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">🌄</div>
    <h2>Wisata Nusantara</h2>
    <div class="subtitle">Bagikan keindahan Indonesia</div>

    <?php if (isset($error)): ?>
        <div class="alert"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Username Anda" required>
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" id="password" placeholder="••••••••" required>
        </div>
        <button type="submit">Masuk ke Akun</button>
    </form>

    <div class="divider"><span>ATAU</span></div>

    <a href="?action=guest" class="btn-guest">🚶 Masuk sebagai Tamu</a>

    <a href="register.php" class="register-link">
        Belum punya akun? <strong>Daftar Sekarang</strong>
    </a>
</div>

</body>
</html>