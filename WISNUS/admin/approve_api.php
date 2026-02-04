<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Sesuaikan path ke PHPMailer Anda
session_start();

// Proteksi: Hanya Admin yang bisa buka file ini
if ($_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak!");
}

if (isset($_GET['username'])) {
    $targetUser = $_GET['username'];
    $file = '../data/api_requests.json';
    $requests = json_decode(file_get_contents($file), true);
    
    $userEmail = ""; // Kita asumsikan email user ada di data/users.json

    // 1. Update Status di JSON
    foreach ($requests as &$req) {
        if ($req['username'] === $targetUser) {
            $req['status'] = 'approved';
            break;
        }
    }
    file_put_contents($file, json_encode($requests, JSON_PRETTY_PRINT));

    // 2. Kirim Notifikasi ke Gmail User
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP Google
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'email-admin-anda@gmail.com'; // Gmail Admin
        $mail->Password   = 'kode-app-password-anda';     // 16 digit App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Penerima & Konten
        $mail->setFrom('email-admin-anda@gmail.com', 'Admin Wisata Nusantara');
        $mail->addAddress($targetUser . "@gmail.com"); // Contoh jika username = email, atau ambil dari data user

        $mail->isHTML(true);
        $mail->Subject = 'Permintaan Akses API Disetujui! 🔑';
        $mail->Body    = "
            <h3>Halo, $targetUser!</h3>
            <p>Permintaan Anda untuk mengakses <b>API Wisata Nusantara</b> telah disetujui oleh Admin.</p>
            <p>Sekarang Anda bisa mengakses data melalui link berikut:</p>
            <a href='http://localhost/project2/api.php'>Buka API Sekarang</a>
            <br><br>
            <p>Terima kasih telah berkontribusi!</p>
        ";

        $mail->send();
        header("Location: dashboard_admin.php?msg=Disetujui dan Email Terkirim");
    } catch (Exception $e) {
        echo "Gagal mengirim email. Error: {$mail->ErrorInfo}";
    }
}
?>