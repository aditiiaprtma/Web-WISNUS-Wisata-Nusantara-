<?php
header("Content-Type: application/json");

// =============================
// PATH FILE
// =============================
$dataFile  = "../data/wisata_pending.json";
$uploadDir = "../uploads/";

// =============================
// AMBIL DATA POST
// =============================
$nama       = $_POST['nama'] ?? '';
$daerah     = $_POST['daerah'] ?? '';
$kategori   = $_POST['kategori'] ?? '';
$deskripsi  = $_POST['deskripsi'] ?? '';

// =============================
// VALIDASI
// =============================
if ($nama === '' || $daerah === '' || $deskripsi === '') {
    echo json_encode([
        "status"  => false,
        "message" => "Data wajib diisi"
    ]);
    exit;
}

// =============================
// UPLOAD GAMBAR (OPSIONAL)
// =============================
$gambar = null;

if (isset($_FILES['gambar']) && !empty($_FILES['gambar']['name'])) {

    // validasi ekstensi
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode([
            "status"  => false,
            "message" => "Format gambar tidak didukung"
        ]);
        exit;
    }

    // nama file unik
    $gambar = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $nama) . "." . $ext;

    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $gambar)) {
        echo json_encode([
            "status"  => false,
            "message" => "Gagal upload gambar"
        ]);
        exit;
    }

    // 👉 simpan RELATIVE PATH (INI PENTING)
    $gambar = "uploads/" . $gambar;
}

// =============================
// BACA DATA JSON LAMA
// =============================
$data = [];

if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $data = json_decode($json, true) ?? [];
}

// =============================
// TAMBAH DATA BARU
// =============================
$data[] = [
    "id"         => time(),
    "nama"       => $nama,
    "daerah"     => $daerah,
    "kategori"   => $kategori,
    "deskripsi"  => $deskripsi,
    "gambar"     => $gambar, // bisa null
    "status"     => "pending",
    "tanggal"    => date("Y-m-d H:i:s")
];

// =============================
// SIMPAN KEMBALI KE JSON
// =============================
file_put_contents(
    $dataFile,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

// =============================
// RESPONSE
// =============================
echo json_encode([
    "status"  => true,
    "message" => "Berhasil, menunggu persetujuan admin"
]);
