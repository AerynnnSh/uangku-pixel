<!DOCTYPE html>
<html>
<head>
    <title>Proses Simpan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="game-container" style="text-align: center;">
        <?php
        include 'koneksi.php';

        $tanggal    = $_POST['tanggal'];
        $jenis      = $_POST['jenis'];
        $kategori   = $_POST['kategori'];
        $jumlah     = $_POST['jumlah'];
        $keterangan = $_POST['keterangan'];

        $query = "INSERT INTO transaksi (tanggal, jenis, kategori, jumlah, keterangan) 
                  VALUES ('$tanggal', '$jenis', '$kategori', '$jumlah', '$keterangan')";

        if (mysqli_query($koneksi, $query)) {
            echo "<h1>✅ SUKSES!</h1>";
            echo "<p>Data berhasil disimpan ke memori.</p><br>";
            echo "<a href='index.php' class='btn'>Kembali ke Menu</a>";
        } else {
            echo "<h1>❌ GAGAL!</h1>";
            echo "<p>Error: " . mysqli_error($koneksi) . "</p>";
        }
        ?>
    </div>
</body>
</html>