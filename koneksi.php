<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "uangku_db";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Gagal connect: " . mysqli_connect_error());
}
?>