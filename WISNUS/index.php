<?php
session_start();

// Ambil Data Pengumuman
$infoFile = "data/info.txt";
$adminInfo = file_exists($infoFile) ? trim(file_get_contents($infoFile)) : "";

// Ambil Data Wisata yang sudah di-Approve Admin
$wisataFile = "data/wisata.json";
$daftarWisata = file_exists($wisataFile) ? json_decode(file_get_contents($wisataFile), true) : [];
if (!is_array($daftarWisata)) $daftarWisata = [];

// CEK STATUS API USER (Untuk tampilan navigasi)
$userApiKey = null;
if (isset($_SESSION['user'])) {
    $apiReqFile = "data/api_requests.json";
    if (file_exists($apiReqFile)) {
        $apiRequests = json_decode(file_get_contents($apiReqFile), true) ?: [];
        foreach ($apiRequests as $req) {
            // Hanya ambil jika statusnya APPROVED
            if ($req['username'] === $_SESSION['user']['username'] && $req['status'] === 'approved') {
                $userApiKey = $req['api_key']; 
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wisata Nusantara | Eksplorasi Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style khusus untuk tombol status API yang bisa diklik */
        .btn-api-status {
            background: rgba(99, 102, 241, 0.1);
            color: #818cf8;
            padding: 8px 15px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(99, 102, 241, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .btn-api-status:hover { 
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">
            <span>🌄</span> Wisata<span>Nusantara</span>
        </a>
        
        <div class="nav-menu">
            <a href="index.php" class="nav-link">Beranda</a>

            <?php if(isset($_SESSION['user'])): ?>
                <?php if($userApiKey): ?>
                    <a href="api.php?key=<?= $userApiKey ?>" target="_blank" class="btn-api-status" title="Klik untuk lihat data JSON">
                        🔑 API: <?= $userApiKey ?>
                    </a>
                <?php else: ?>
                    <a href="request_api.php" class="btn-api">🔑 Request API Key</a>
                <?php endif; ?>
                
                <a href="upload.php" class="nav-link btn-upload">➕ Tambah Wisata</a>
            <?php else: ?>
                <a href="request_api.php" class="btn-api">🔑 Request API Key</a>
                <button onclick="checkLoginBeforeUpload()" class="btn-upload">➕ Tambah Wisata</button>
            <?php endif; ?>

            <div class="user-info">
                <?php if(isset($_SESSION['user'])): ?>
                    <span class="user-name">
                        Hi, <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>
                    </span>
                    <?php if($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="admin/dashboard.php" class="nav-link admin-link" style="color:#6366f1;">Admin Panel</a>
                    <?php endif; ?>
                    <a href="user/logout.php" class="nav-link logout-link" style="color: #ef4444;">Keluar</a>
                <?php else: ?>
                    <a href="user/login.php" class="nav-link">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if($adminInfo): ?>
    <div class="admin-info">
        <div class="scrolling-text">
            📢 <strong>PENGUMUMAN:</strong> <?= htmlspecialchars($adminInfo) ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            📢 <strong>PENGUMUMAN:</strong> <?= htmlspecialchars($adminInfo) ?>
        </div>
    </div>
<?php endif; ?>

<header class="hero">
    <div class="container">
        <span class="pre-title">Wonderful Indonesia</span>
        <h1 class="main-title">Temukan Destinasi <br><span>Liburan Terbaik</span></h1>
        <p class="subtitle">Jelajahi keajaiban alam dan budaya Indonesia yang belum pernah Anda bayangkan sebelumnya.</p>
        
        <div class="search-container">
            <div class="search-box-flex">
                <div class="search-input-wrap">
                    <span>🔍</span>
                    <input id="title" type="text" placeholder="Cari pantai, gunung, atau kota..." onkeyup="searchMovie()">
                </div>
                <div class="filter-wrap">
                    <select id="kategori" onchange="searchMovie()">
                        <option value="">🌿 Semua Kategori</option>
                        <option value="Panorama">⛰️ Panorama</option>
                        <option value="Sejarah">🏛️ Sejarah</option>
                        <option value="Sungai">🏞️ Sungai</option>
                    </select>
                </div>
                <button class="btn-search" onclick="searchMovie()">Cari</button>
            </div>
        </div>
    </div>
</header>

<main class="container" style="padding: 40px 0;">
    <section id="result" class="movie-grid">
        <?php if(empty($daftarWisata)): ?>
            <div id="no-results" style="text-align:center; grid-column: 1/-1; padding: 100px 0; color: #64748b;">
                <p style="font-size: 18px;">Belum ada destinasi wisata yang tersedia.</p>
            </div>
        <?php else: ?>
            <?php foreach ($daftarWisata as $w): ?>
                <div class="movie-card wisata-item" 
                     data-nama="<?= strtolower(htmlspecialchars($w['nama'] . ' ' . $w['daerah'])) ?>" 
                     data-kategori="<?= htmlspecialchars($w['kategori']) ?>">
                    
                    <span class="badge"><?= htmlspecialchars($w['kategori']) ?></span>
                    <img src="<?= htmlspecialchars($w['gambar']) ?>" alt="<?= htmlspecialchars($w['nama']) ?>" onerror="this.src='https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800'">
                    
                    <div class="movie-info">
                        <h3><?= htmlspecialchars($w['nama']) ?></h3>
                        <p>📍 <?= htmlspecialchars($w['daerah']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<script>
    function checkLoginBeforeUpload() {
        alert("Silakan Login terlebih dahulu untuk menambahkan wisata.");
        window.location.href = "user/login.php";
    }

    function searchMovie() {
        const titleInput = document.getElementById('title').value.toLowerCase();
        const categoryInput = document.getElementById('kategori').value;
        const items = document.querySelectorAll('.wisata-item');
        let found = false;

        items.forEach(item => {
            const nama = item.getAttribute('data-nama');
            const kategori = item.getAttribute('data-kategori');
            const matchTitle = nama.includes(titleInput);
            const matchCategory = categoryInput === "" || kategori === categoryInput;

            if (matchTitle && matchCategory) {
                item.style.display = "block";
                found = true;
            } else {
                item.style.display = "none";
            }
        });

        let noResult = document.getElementById('no-results-msg');
        if (!found) {
            if (!noResult) {
                const msg = document.createElement('div');
                msg.id = "no-results-msg";
                msg.style.cssText = "text-align:center; grid-column:1/-1; padding:50px; color:#94a3b8;";
                msg.innerHTML = "Destinasi tidak ditemukan.";
                document.getElementById('result').appendChild(msg);
            }
        } else if (noResult) {
            noResult.remove();
        }
    }
</script>
</body>
</html>