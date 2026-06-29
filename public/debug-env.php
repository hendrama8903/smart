<?php
// HAPUS FILE INI SETELAH DEBUG SELESAI
$keys = ['DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','APP_ENV'];
foreach ($keys as $k) {
    echo $k . ": " . (getenv($k) ?: $_ENV[$k] ?? 'TIDAK ADA') . "<br>";
}
