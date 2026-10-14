<?php
session_start();

define('FILE_USER', __DIR__ . '/data/users.json');

function ambilUser()
{
    if (!file_exists(FILE_USER)) {
        return [];
    }
    $data = json_decode(file_get_contents(FILE_USER), true);
    return is_array($data) ? $data : [];
}

function simpanUser($users)
{
    $folder = dirname(FILE_USER);
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
    file_put_contents(FILE_USER, json_encode(array_values($users), JSON_PRETTY_PRINT), LOCK_EX);
}

function cariByEmail($email)
{
    foreach (ambilUser() as $u) {
        if (strtolower($u['email']) === strtolower($email)) {
            return $u;
        }
    }
    return null;
}

function cariById($id)
{
    foreach (ambilUser() as $u) {
        if ($u['id'] == $id) {
            return $u;
        }
    }
    return null;
}

function updateUser($id, $dataBaru)
{
    $users = ambilUser();
    foreach ($users as &$u) {
        if ($u['id'] == $id) {
            $u = array_merge($u, $dataBaru);
            break;
        }
    }
    unset($u);
    simpanUser($users);
}

function bersih($teks)
{
    return htmlspecialchars(trim($teks), ENT_QUOTES, 'UTF-8');
}

function setPesan($jenis, $teks)
{
    $_SESSION['pesan'] = ['jenis' => $jenis, 'teks' => $teks];
}

function tampilPesan()
{
    if (empty($_SESSION['pesan'])) {
        return;
    }
    $p = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
    echo '<div class="alert ' . $p['jenis'] . '">' . $p['teks'] . '</div>';
}

// kalau belum ada session tapi ada cookie remember, coba login otomatis
function loginOtomatis()
{
    if (isset($_SESSION['user_id']) || empty($_COOKIE['remember'])) {
        return;
    }

    $bagian = explode(':', $_COOKIE['remember'], 2);
    if (count($bagian) !== 2) {
        return;
    }

    list($id, $token) = $bagian;
    $user = cariById($id);

    if ($user && !empty($user['remember_hash'])
        && hash_equals($user['remember_hash'], hash('sha256', $token))) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
    }
}

function wajibLogin()
{
    loginOtomatis();

    if (!isset($_SESSION['user_id']) || !cariById($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}