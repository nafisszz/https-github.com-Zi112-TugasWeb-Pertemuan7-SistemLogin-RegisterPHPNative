<?php
require 'functions.php';

loginOtomatis();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$email = '';

if (isset($_GET['keluar'])) {
    setPesan('info', 'Kamu sudah logout.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailAsli = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = bersih($emailAsli);

    if ($emailAsli === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } elseif (!filter_var($emailAsli, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $user = cariByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];

            if (!empty($_POST['ingat'])) {
                $token = bin2hex(random_bytes(16));
                updateUser($user['id'], ['remember_hash' => hash('sha256', $token)]);
                setcookie('remember', $user['id'] . ':' . $token, [
                    'expires' => time() + 60 * 60 * 24 * 30,
                    'path' => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Email atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="kotak">
        <h1>Login</h1>
        <p class="sub">Masuk dengan akun yang sudah terdaftar.</p>

        <?php tampilPesan(); ?>

        <?php if ($error !== ''): ?>
            <div class="alert gagal"><?= $error ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= $email ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label class="centang">
                <input type="checkbox" name="ingat" value="1"> Ingat saya selama 30 hari
            </label>

            <button type="submit">Login</button>
        </form>

        <p class="bawah">Belum punya akun? <a href="register.php">Daftar dulu</a></p>
    </main>
</body>
</html>