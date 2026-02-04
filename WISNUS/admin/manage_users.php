<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit;
}

$file = __DIR__ . "/../data/users.json";
$users = json_decode(file_get_contents($file), true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna | Admin</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { background: #0f172a; color: white; padding: 40px; font-family: 'Inter', sans-serif; }
        .admin-card { background: #1e293b; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h2 { color: #6366f1; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #0f172a; color: #94a3b8; }
        .btn-delete { color: #ef4444; text-decoration: none; font-weight: bold; padding: 5px 10px; border: 1px solid #ef4444; border-radius: 8px; transition: 0.3s; }
        .btn-delete:hover { background: #ef4444; color: white; }
        .badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .badge-admin { background: #6366f1; }
        .badge-user { background: #10b981; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #94a3b8; text-decoration: none; }
    </style>
</head>
<body>

<div class="admin-card">
    <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
    <h2>👥 Manajemen Pengguna</h2>
    
    <?php if(isset($_GET['msg'])): ?>
        <p style="color: #10b981; margin-bottom: 15px;">✔️ <?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                <td>
                    <span class="badge <?= $u['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                        <?= strtoupper($u['role']) ?>
                    </span>
                </td>
                <td><?= $u['created_at'] ?? '-' ?></td>
                <td>
                    <?php if($u['role'] !== 'admin'): ?>
                        <a href="delete_user.php?id=<?= $u['id'] ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Yakin ingin menghapus user ini? Semua data mereka akan hilang.')">
                           Hapus Akun
                        </a>
                    <?php else: ?>
                        <span style="color: #475569; font-size: 12px;">Utama (Tidak bisa dihapus)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>