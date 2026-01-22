<!DOCTYPE html>
<html>
<head>
    <title>Uangku - Pixel Edition</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="game-container">

        <h2>Aplikasi Pencatat Keuangan 💰</h2>

        <form action="tambah.php" method="POST">
            <label>Tanggal:</label>
            <input type="date" name="tanggal" required>
            
            <label>Jenis:</label>
            <select name="jenis">
                <option value="Pemasukan">Pemasukan (+)</option>
                <option value="Pengeluaran">Pengeluaran (-)</option>
            </select>
            
            <label>Kategori:</label>
            <input type="text" name="kategori" placeholder="Contoh: Makan, Gaji" required>
            
            <label>Jumlah (Rp):</label>
            <input type="number" name="jumlah" required>
            
            <label>Keterangan:</label>
            <textarea name="keterangan"></textarea>
            
            <button type="submit">Simpan Transaksi</button>
        </form>

        <hr>
        
        <?php
        // --- PERBAIKAN DI SINI ---
        // Kita panggil koneksi DULUAN sebelum minta data ke database
        include 'koneksi.php'; 

        // 1. Hitung Total Pemasukan
        $queryMasuk = "SELECT SUM(jumlah) AS total_masuk FROM transaksi WHERE jenis='Pemasukan'";
        $resultMasuk = mysqli_query($koneksi, $queryMasuk);
        $rowMasuk = mysqli_fetch_assoc($resultMasuk);
        $totalMasuk = $rowMasuk['total_masuk'] ?? 0;

        // 2. Hitung Total Pengeluaran
        $queryKeluar = "SELECT SUM(jumlah) AS total_keluar FROM transaksi WHERE jenis='Pengeluaran'";
        $resultKeluar = mysqli_query($koneksi, $queryKeluar);
        $rowKeluar = mysqli_fetch_assoc($resultKeluar);
        $totalKeluar = $rowKeluar['total_keluar'] ?? 0;

        // 3. Hitung Saldo Akhir
        $saldo = $totalMasuk - $totalKeluar;
        ?>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <div style="background: #2d1b4e; color: #00e676; padding: 15px; width: 33%; border: 4px solid #000;">
                <small>PEMASUKAN</small><br>
                Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?>
            </div>
            <div style="background: #2d1b4e; color: #ff1744; padding: 15px; width: 33%; border: 4px solid #000;">
                <small>PENGELUARAN</small><br>
                Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?>
            </div>
            <div style="background: #2d1b4e; color: #fff; padding: 15px; width: 33%; border: 4px solid #000;">
                <small>SISA SALDO</small><br>
                Rp <?php echo number_format($saldo, 0, ',', '.'); ?>
            </div>
        </div>
        
        <h3>Riwayat Transaksi 📜</h3>

        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Ket.</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Di sini tidak perlu include 'koneksi.php' lagi karena sudah di atas.
                // Langsung pakai saja variabel $koneksi-nya.
                
                $query = "SELECT * FROM transaksi ORDER BY id DESC";
                $result = mysqli_query($koneksi, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['tanggal'] . "</td>";
                    
                    // Warna teks hijau/merah biar lebih jelas
                    $warna = ($row['jenis'] == 'Pemasukan') ? 'green' : 'red';
                    echo "<td style='color:$warna;'>" . $row['jenis'] . "</td>";
                    
                    echo "<td>" . $row['kategori'] . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                    echo "<td>" . $row['keterangan'] . "</td>";
                    echo "<td>
                            <a href='edit.php?id=" . $row['id'] . "'>Edit</a>
                            <br><br>
                            <a href='hapus.php?id=" . $row['id'] . "'>Hapus</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

    </div> 
</body>
</html>