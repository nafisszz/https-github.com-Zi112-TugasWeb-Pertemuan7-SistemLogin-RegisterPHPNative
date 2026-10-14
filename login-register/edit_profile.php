<?php
require 'functions.php';
wajibLogin();

$user = cariById($_SESSION['user_id']);
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaAsli = trim($_POST['nama'] ?? '');
    $passLama = $_POST['password_lama'] ?? '';
    $passBaru = $_POST['password_baru'] ?? '';

    if ($namaAsli === '') {
        $error[] = 'Nama tidak boleh kosong.';
    } elseif (mb_strlen($namaAsli) < 3) {
        $error[] = 'Nama minimal 3 karakter.';
    }

    // password hanya diganti kalau kolom password baru diisi
    if ($passBaru !== '') {
        if (!password_verify($passLama, $user['password'])) {
            $error[] = 'Password lama salah.';
        } elseif (strlen($passBaru) < 6) {
            $error[] = 'Password baru minimal 6 karakter.';
        }
    }

    if (empty($error)) {
        $dataBaru = ['nama' => bersih($namaAsli)];

        if ($passBaru !== '') {
            $dataBaru['password'] = password_hash($passBaru, PASSWORD_DEFAULT);
        }

        updateUser($user['id'], $dataBaru);
        setPesan('sukses', 'Profil berhasil diperbarui.');
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="kotak">
        <h1>Edit Profil</h1>
        <p class="sub">Kosongkan kolom password jika tidak ingin menggantinya.</p>

        <?php if (!empty($error)): ?>
            <div class="alert gagal">
                <ul>
                    <?php foreach ($error as $e): ?>
                        <li><?= $e ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="edit_profile.php">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" value="<?= $user['nama'] ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" value="<?= $user['email'] ?>" disabled>

            <label for="password_lama">Password Lama</label>
            <input type="password" id="password_lama" name="password_lama">

            <label for="password_baru">Password Baru</label>
            <input type="password" id="password_baru" name="password_baru">

            <button type="submit">Simpan</button>
        </form>

        <p class="bawah"><a href="dashboard.php">Kembali ke dashboard</a></p>
    </main>
</body>
</html>