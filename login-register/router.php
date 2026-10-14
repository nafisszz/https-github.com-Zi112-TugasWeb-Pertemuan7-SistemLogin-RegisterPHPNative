<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// blokir akses langsung ke folder data
if (stripos($path, '/data/') === 0) {
    http_response_code(403);
    echo '403 Forbidden';
    return true;
}

// selain itu, biarkan server bawaan melayani file seperti biasa
return false;