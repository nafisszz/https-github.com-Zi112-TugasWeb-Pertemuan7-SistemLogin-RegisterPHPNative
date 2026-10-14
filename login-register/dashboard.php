<?php
require 'functions.php';
wajibLogin();

$user = cariById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="kotak">
        <h1>Halo, <?= $user['nama'] ?></h1>
        <p class="sub">Kamu berhasil masuk ke halaman yang diproteksi.</p>

        <?php tampilPesan(); ?>

        <table class="info">
            <tr>
                <th>Nama</th>
                <td><?= $user['nama'] ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= $user['email'] ?></td>
            </tr>
            <tr>
                <th>Terdaftar</th>
                <td><?= date('d-m-Y H:i', strtotime($user['dibuat'])) ?></td>
            </tr>
        </table>

        <div class="aksi">
            <a href="edit_profile.php" class="tombol">Edit Profil</a>
            <a href="logout.php" class="tombol merah">Logout</a>
        </div>
    </main>
</body>
</html>