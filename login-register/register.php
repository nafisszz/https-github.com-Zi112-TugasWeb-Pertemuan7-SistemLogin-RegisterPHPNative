<?php
require 'functions.php';

loginOtomatis();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = [];
$nama = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaAsli = trim($_POST['nama'] ?? '');
    $emailAsli = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if ($namaAsli === '') {
        $error[] = 'Nama tidak boleh kosong.';
    } elseif (mb_strlen($namaAsli) < 3) {
        $error[] = 'Nama minimal 3 karakter.';
    }

    if ($emailAsli === '') {
        $error[] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($emailAsli, FILTER_VALIDATE_EMAIL)) {
        $error[] = 'Format email tidak valid.';
    } elseif (cariByEmail(bersih($emailAsli))) {
        $error[] = 'Email sudah terdaftar, silakan login.';
    }

    if (strlen($password) < 6) {
        $error[] = 'Password minimal 6 karakter.';
    }

    if ($password !== $konfirmasi) {
        $error[] = 'Konfirmasi password tidak sama.';
    }

    $nama = bersih($namaAsli);
    $email = bersih($emailAsli);

    if (empty($error)) {
        $users = ambilUser();
        $idBaru = $users ? max(array_column($users, 'id')) + 1 : 1;

        $users[] = [
            'id' => $idBaru,
            'nama' => $nama,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'remember_hash' => '',
            'dibuat' => date('Y-m-d H:i:s'),
        ];
        simpanUser($users);

        setPesan('sukses', 'Registrasi berhasil, silakan login.');
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="kotak">
        <h1>Daftar Akun</h1>
        <p class="sub">Buat akun baru untuk masuk ke dashboard.</p>

        <?php if (!empty($error)): ?>
            <div class="alert gagal">
                <ul>
                    <?php foreach ($error as $e): ?>
                        <li><?= $e ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" value="<?= $nama ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= $email ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="konfirmasi">Konfirmasi Password</label>
            <input type="password" id="konfirmasi" name="konfirmasi" required>

            <button type="submit">Daftar</button>
        </form>

        <p class="bawah">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </main>
</body>
</html>