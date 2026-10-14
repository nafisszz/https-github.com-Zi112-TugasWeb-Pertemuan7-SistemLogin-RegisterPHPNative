<?php
require 'functions.php';

if (isset($_SESSION['user_id'])) {
    updateUser($_SESSION['user_id'], ['remember_hash' => '']);
}

setcookie('remember', '', time() - 3600, '/');

$_SESSION = [];
session_destroy();

header('Location: login.php?keluar=1');
exit;