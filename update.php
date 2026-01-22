<?php
include 'koneksi.php';

$id         = $_POST['id'];
$tanggal    = $_POST['tanggal'];
$jenis      = $_POST['jenis'];
$kategori   = $_POST['kategori'];
$jumlah     = $_POST['jumlah'];
$keterangan = $_POST['keterangan'];

$query = "UPDATE transaksi SET 
            tanggal='$tanggal', 
            jenis='$jenis', 
            kategori='$kategori', 
            jumlah='$jumlah', 
            keterangan='$keterangan' 
          WHERE id='$id'";

if (mysqli_query($koneksi, $query)) {
    header("location:index.php"); // Kalau sukses langsung balik
} else {
    echo "Gagal update: " . mysqli_error($koneksi);
}
?>