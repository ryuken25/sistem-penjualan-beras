<?php

mysqli_report(MYSQLI_REPORT_OFF);

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$port = 3306;

$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);

if (!$mysqli->real_connect($host, $user, $pass, null, $port)) {
    fwrite(STDOUT, 'CONNECT_ERROR:' . mysqli_connect_error() . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, 'CONNECTED' . PHP_EOL);

if (!$mysqli->query('CREATE DATABASE IF NOT EXISTS sistem_penjualan_beras CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci')) {
    fwrite(STDOUT, 'CREATE_DB_ERROR:' . $mysqli->error . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, 'DB_READY' . PHP_EOL);

$result = $mysqli->query('SHOW DATABASES LIKE "sistem_penjualan_beras"');

if ($result === false) {
    fwrite(STDOUT, 'SHOW_DB_ERROR:' . $mysqli->error . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, 'DATABASE_FOUND:' . $result->num_rows . PHP_EOL);
