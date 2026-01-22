<?php
session_start();
include 'koneksi.php';

// Cek Login
if(!isset($_SESSION['status'])) { header("location:login.php"); exit(); }

// Tangkap Filter dari URL (biar yang didownload sesuai yang dilihat)
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// MANTRA AJAIB: Header ini memaksa browser download file excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_$bulan-$tahun.xls");
?>

<h3>LAPORAN KEUANGAN BULAN <?php echo "$bulan / $tahun"; ?></h3>

<table border="1">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM users JOIN transaksi ON 1=1 
                  WHERE MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun' 
                  ORDER BY tanggal DESC";
        // Catatan: Idealnya pakai User ID, tapi karena sederhana kita ambil semua dulu
        $result = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun' ORDER BY tanggal DESC");
        
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['tanggal'] . "</td>";
            echo "<td>" . $row['jenis'] . "</td>";
            echo "<td>" . $row['kategori'] . "</td>";
            echo "<td>" . $row['jumlah'] . "</td>";
            echo "<td>" . $row['keterangan'] . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>