<?php

session_start();



/* =====================

   1. PROTEKSI ADMIN

===================== */

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {

    header("Location: ../user/login.php"); 

    exit;

}



/* =====================

   2. DATA & PATH

===================== */

$logFile     = __DIR__ . "/../data/log.txt";

$infoFile    = __DIR__ . "/../data/info.txt";

$pendingFile = __DIR__ . "/../data/wisata_pending.json";

$activeFile  = __DIR__ . "/../data/wisata.json";

$userFile    = __DIR__ . "/../data/users.json";

$apiReqFile  = __DIR__ . "/../data/api_requests.json"; 



// Update Pengumuman

$successMsg = "";

if (isset($_POST['save_info'])) {

    file_put_contents($infoFile, trim($_POST['info']), LOCK_EX);

    $successMsg = "Informasi berhasil diperbarui!";

}



// Load Data

$pendingData  = file_exists($pendingFile) ? json_decode(file_get_contents($pendingFile), true) : [];

$activeData   = file_exists($activeFile) ? json_decode(file_get_contents($activeFile), true) : [];

$userData     = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

$apiRequests  = file_exists($apiReqFile) ? json_decode(file_get_contents($apiReqFile), true) : []; 

$infoContent  = file_exists($infoFile) ? file_get_contents($infoFile) : '';

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Admin Panel | Wisata Nusantara</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>

        :root {

            --bg: #0f172a;

            --card: rgba(30, 41, 59, 0.7);

            --primary: #6366f1;

            --danger: #ef4444;

            --success: #10b981;

            --warning: #fbbf24;

            --text: #f8fafc;

        }

        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body { background: var(--bg); color: var(--text); margin: 0; padding: 20px; line-height: 1.6; }

        .container { max-width: 1200px; margin: auto; }

        .card { 

            background: var(--card); backdrop-filter: blur(10px); 

            border: 1px solid rgba(255,255,255,0.1); 

            border-radius: 16px; padding: 24px; margin-bottom: 24px; 

        }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }

        .stat-val { font-size: 2rem; font-weight: 700; color: var(--primary); }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }

        th { text-align: left; padding: 12px; border-bottom: 2px solid rgba(255,255,255,0.1); color: var(--primary); font-size: 0.8rem; text-transform: uppercase; }

        td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem; vertical-align: middle; }

        textarea { width: 100%; height: 80px; background: rgba(0,0,0,0.2); border: 1px solid #334155; border-radius: 8px; color: white; padding: 10px; resize: none; margin-bottom: 10px; }

        .btn { padding: 8px 14px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; transition: 0.2s; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; }

        .btn-primary { background: var(--primary); color: white; }

        .btn-danger { background: var(--danger); color: white; }

        .btn-success { background: var(--success); color: white; }

        .btn-warning { background: var(--warning); color: #000; }

        .btn-outline { background: transparent; border: 1px solid var(--primary); color: var(--primary); }

        .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; }

        .badge-pending { background: rgba(251, 191, 36, 0.1); color: var(--warning); }

        .badge-approved { background: rgba(16, 185, 129, 0.1); color: var(--success); }

        .badge-revoked { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

        code { background: #000; padding: 4px 8px; border-radius: 4px; color: #06b6d4; font-family: monospace; }

        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }

    </style>

</head>

<body>



<div class="container">

    <header>

        <div>

            <h1 style="margin:0;">Admin <span style="color:var(--primary)">Dashboard</span></h1>

            <p style="font-size: 0.9rem; color: #94a3b8; margin: 5px 0 0 0;">Selamat datang kembali, <?= htmlspecialchars($_SESSION['user']['username']) ?></p>

        </div>

        <div style="display: flex; gap: 10px;">

            <a href="../api.php" class="btn btn-outline">Lihat API</a>

            <a href="manage_users.php" class="btn btn-outline">👥 Kelola User</a>

            <a href="../user/logout.php" class="btn btn-danger">Keluar</a>

        </div>

    </header>



    <div class="grid" style="margin-bottom:24px;">

        <div class="card">

            <div style="opacity:0.7; font-size: 0.8rem;">Total User</div>

            <div class="stat-val"><?= count($userData) ?></div>

        </div>

        <div class="card">

            <div style="opacity:0.7; font-size: 0.8rem;">Antrean Wisata</div>

            <div class="stat-val" style="color:var(--warning)"><?= count($pendingData) ?></div>

        </div>

        <div class="card">

            <div style="opacity:0.7; font-size: 0.8rem;">Wisata Terbit</div>

            <div class="stat-val" style="color:var(--success)"><?= count($activeData) ?></div>

        </div>

    </div>



    <div class="card">

        <h3>🔑 Monitoring API Key</h3>

        <div style="overflow-x: auto;">

            <table>

                <thead>

                    <tr><th>User</th><th>API Key</th><th>Status</th><th>Aksi</th></tr>

                </thead>

                <tbody>

                    <?php if(empty($apiRequests)): ?>

                        <tr><td colspan="4" style="text-align:center; opacity:0.5; padding: 20px;">Belum ada request.</td></tr>

                    <?php else: ?>

                        <?php foreach ($apiRequests as $req): ?>

                        <tr>

                            <td><strong><?= htmlspecialchars($req['username']) ?></strong></td>

                            <td><code><?= $req['api_key'] ?></code></td>

                            <td><span class="badge badge-<?= $req['status'] ?>"><?= strtoupper($req['status']) ?></span></td>

                            <td>

                                <?php if($req['status'] === 'pending'): ?>

                                    <button onclick="handleAPI('<?= $req['username'] ?>', 'approve')" class="btn btn-success">Setujui</button>

                                <?php elseif($req['status'] === 'approved'): ?>

                                    <button onclick="handleAPI('<?= $req['username'] ?>', 'revoke')" class="btn btn-danger">Matikan</button>

                                <?php else: ?>

                                    <button onclick="handleAPI('<?= $req['username'] ?>', 'approve')" class="btn btn-warning">Aktifkan</button>

                                <?php endif; ?>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>



    <div class="card">

        <h3>📢 Pengumuman Website</h3>

        <form method="post">

            <textarea name="info" placeholder="Tulis pengumuman..."><?= htmlspecialchars($infoContent) ?></textarea>

            <button name="save_info" class="btn btn-primary">Update Pengumuman</button>

        </form>

    </div>



    <div class="card">

        <h3>⏳ Menunggu Persetujuan Wisata</h3>

        <div style="overflow-x: auto;">

            <table>

                <thead>

                    <tr><th>Nama Wisata</th><th>Daerah</th><th>Pengirim</th><th>Aksi</th></tr>

                </thead>

                <tbody>

                    <?php if(empty($pendingData)): ?>

                        <tr><td colspan="4" style="text-align:center; opacity:0.5; padding: 20px;">Antrean bersih!</td></tr>

                    <?php else: ?>

                        <?php foreach ($pendingData as $w): ?>

                        <tr>

                            <td><strong><?= htmlspecialchars($w['nama']) ?></strong></td>

                            <td><?= htmlspecialchars($w['daerah']) ?></td>

                            <td><span class="badge" style="background:rgba(255,255,255,0.1);"><?= htmlspecialchars($w['user']) ?></span></td>

                            <td>

                                <button onclick="manage(<?= $w['id'] ?>, 'approve')" class="btn btn-success">Terima</button>

                                <button onclick="manage(<?= $w['id'] ?>, 'reject')" class="btn btn-danger">Tolak</button>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>



    <div class="card">

        <h3>✅ Objek Wisata Aktif (Live)</h3>

        <div style="overflow-x: auto; max-height: 400px;">

            <table>

                <thead>

                    <tr><th>Nama Wisata</th><th>Kategori</th><th>Daerah</th><th>Aksi</th></tr>

                </thead>

                <tbody>

                    <?php if(empty($activeData)): ?>

                        <tr><td colspan="4" style="text-align:center; opacity:0.5; padding: 20px;">Belum ada wisata yang terbit.</td></tr>

                    <?php else: ?>

                        <?php foreach ($activeData as $w): ?>

                        <tr>

                            <td><strong><?= htmlspecialchars($w['nama']) ?></strong></td>

                            <td><span class="badge badge-approved"><?= htmlspecialchars($w['kategori']) ?></span></td>

                            <td><?= htmlspecialchars($w['daerah']) ?></td>

                            <td>

                                <button onclick="manage(<?= $w['id'] ?>, 'delete')" class="btn btn-danger" title="Hapus dari Halaman Utama">

                                    🗑️ Hapus

                                </button>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>



<script>

// Fungsi Manajemen Wisata (Approve, Reject, Delete)

async function manage(id, action) {

    let confirmMsg = action === 'delete' ? "Hapus wisata ini secara permanen dari halaman utama?" : "Lanjutkan aksi ini?";

    if(!confirm(confirmMsg)) return;



    const formData = new FormData();

    formData.append('id', id);

    formData.append('action', action);



    try {

        const res = await fetch('admin_approve.php', { method: 'POST', body: formData });

        const result = await res.json();

        if (result.status === 'success') {

            location.reload();

        } else {

            alert("Gagal: " + result.message);

        }

    } catch (e) {

        alert("Terjadi kesalahan sistem.");

    }

}



// Fungsi Manajemen API

async function handleAPI(username, action) {

    let msg = action === 'approve' ? "Aktifkan API user ini?" : "Matikan akses API user ini?";

    if(!confirm(msg)) return;



    const formData = new FormData();

    formData.append('username', username);

    formData.append('action', action);



    const res = await fetch('admin_handle_api.php', { method: 'POST', body: formData });

    const result = await res.json();

    alert(result.message);

    location.reload();

}

</script>

</body>

</html>