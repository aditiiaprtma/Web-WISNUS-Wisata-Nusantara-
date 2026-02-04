<?php
session_start();

// Jika belum login sama sekali (bahkan bukan tamu), arahkan ke login
if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
    exit;
}

$isGuest = ($_SESSION['user']['role'] === 'guest');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kontribusi Wisata | Wisata Nusantara</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --primary: #6366f1;
            --text: #f8fafc;
            --input: rgba(15, 23, 42, 0.6);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        
        body {
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        .container { max-width: 600px; width: 100%; }

        /* Card Styling */
        .card {
            background: var(--card);
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        h2 { margin-bottom: 10px; font-weight: 700; color: #fff; }
        p.desc { color: #94a3b8; font-size: 14px; margin-bottom: 30px; }

        /* Guest Notice Overlay */
        .guest-overlay {
            background: rgba(99, 102, 241, 0.1);
            border: 1px dashed var(--primary);
            padding: 20px;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 25px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { border-color: var(--primary); }
            50% { border-color: transparent; }
            100% { border-color: var(--primary); }
        }

        /* Form Styling */
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; font-size: 13px; color: #cbd5e1; margin-bottom: 8px; font-weight: 600; }
        
        .input-group input, .input-group select, .input-group textarea {
            width: 100%; padding: 14px; border-radius: 12px;
            background: var(--input); border: 1px solid #334155;
            color: white; font-size: 15px; transition: 0.3s;
        }

        .input-group input:focus, .input-group textarea:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        textarea { height: 100px; resize: none; }

        .btn {
            width: 100%; padding: 16px; border: none; border-radius: 12px;
            font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 15px;
        }

        .btn-primary { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3); }

        .btn-disabled { background: #334155; color: #94a3b8; cursor: not-allowed; }

        .nav-back { margin-top: 20px; font-size: 14px; color: #64748b; text-decoration: none; display: inline-block; }
        .nav-back:hover { color: var(--primary); }

        /* Disabled state styling */
        input:disabled, textarea:disabled, select:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <a href="index.php" class="nav-back">← Kembali ke Beranda</a>
        <h2 style="margin-top:15px;">📍 Tambah Wisata</h2>
        <p class="desc">Bantu orang lain menemukan tempat indah yang Anda ketahui.</p>

        <?php if ($isGuest): ?>
            <div class="guest-overlay">
                <p style="color: #cbd5e1; font-size: 14px; margin-bottom: 10px;">
                    👋 Halo <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>! <br>
                    Tamu hanya bisa melihat formulir. Silakan daftar untuk mulai berkontribusi.
                </p>
                <a href="user/register.php" style="color: var(--primary); font-weight: bold; text-decoration: none;">🚀 Daftar Akun Gratis</a>
            </div>
        <?php endif; ?>

        <form id="formWisata">
            <div class="input-group">
                <label>Nama Tempat Wisata</label>
                <input type="text" name="nama" placeholder="Misal: Kawah Ijen" required <?= $isGuest ? 'disabled' : '' ?>>
            </div>

            <div class="input-group">
                <label>Daerah / Lokasi</label>
                <input type="text" name="daerah" placeholder="Misal: Banyuwangi, Jatim" required <?= $isGuest ? 'disabled' : '' ?>>
            </div>

            <div class="input-group">
                <label>Kategori</label>
                <select name="kategori" required <?= $isGuest ? 'disabled' : '' ?>>
                    <option value="Panorama">Panorama</option>
                    <option value="Sejarah">Sejarah</option>
                    <option value="Sungai">Sungai</option>
                </select>
            </div>

            <div class="input-group">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" placeholder="Apa yang menarik dari tempat ini?" required <?= $isGuest ? 'disabled' : '' ?>></textarea>
            </div>

            <div class="input-group">
                <label>Foto Wisata</label>
                <input type="file" name="gambar" accept="image/*" required <?= $isGuest ? 'disabled' : '' ?>>
            </div>

            <?php if ($isGuest): ?>
                <button type="button" class="btn btn-disabled">Daftar Akun Untuk Mengirim</button>
            <?php else: ?>
                <button type="submit" class="btn btn-primary">Kirim untuk Review Admin</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
const form = document.getElementById('formWisata');
if (form && !<?= $isGuest ? 'true' : 'false' ?>) {
    form.onsubmit = async (e) => {
        e.preventDefault();
        const btn = form.querySelector('button');
        btn.innerText = "Mengirim...";
        btn.disabled = true;

        const formData = new FormData(form);
        
        try {
            const response = await fetch('api/submit_wisata.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            alert(result.message);
            if(result.status === 'success') {
                location.href = 'index.php';
            } else {
                btn.innerText = "Kirim untuk Review Admin";
                btn.disabled = false;
            }
        } catch (error) {
            alert("Gagal terhubung ke server.");
            btn.disabled = false;
        }
    };
}
</script>

</body>
</html>