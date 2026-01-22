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

        <?php
        // Ambil data dari URL (?bulan=...&tahun=...)
        // Kalau tidak ada, pakai bulan & tahun SEKARANG (default)
        $bulan_dipilih = $_GET['bulan'] ?? date('m');
        $tahun_dipilih = $_GET['tahun'] ?? date('Y');
        ?>

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

        <form action="" method="GET" style="text-align: center; border:none; background: transparent; padding:10px;">
            <label style="display:inline-block; margin-right: 10px;">Filter Laporan:</label>
            
            <select name="bulan" style="width: auto; display:inline-block;">
                <option value="01" <?php if($bulan_dipilih=='01') echo 'selected'; ?>>Januari</option>
                <option value="02" <?php if($bulan_dipilih=='02') echo 'selected'; ?>>Februari</option>
                <option value="03" <?php if($bulan_dipilih=='03') echo 'selected'; ?>>Maret</option>
                <option value="04" <?php if($bulan_dipilih=='04') echo 'selected'; ?>>April</option>
                <option value="05" <?php if($bulan_dipilih=='05') echo 'selected'; ?>>Mei</option>
                <option value="06" <?php if($bulan_dipilih=='06') echo 'selected'; ?>>Juni</option>
                <option value="07" <?php if($bulan_dipilih=='07') echo 'selected'; ?>>Juli</option>
                <option value="08" <?php if($bulan_dipilih=='08') echo 'selected'; ?>>Agustus</option>
                <option value="09" <?php if($bulan_dipilih=='09') echo 'selected'; ?>>September</option>
                <option value="10" <?php if($bulan_dipilih=='10') echo 'selected'; ?>>Oktober</option>
                <option value="11" <?php if($bulan_dipilih=='11') echo 'selected'; ?>>November</option>
                <option value="12" <?php if($bulan_dipilih=='12') echo 'selected'; ?>>Desember</option>
            </select>
            
            <select name="tahun" style="width: auto; display:inline-block;">
                <?php
                $tahun_sekarang = date('Y');
                for($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--){
                    $pilih = ($tahun_dipilih == $i) ? 'selected' : '';
                    echo "<option value='$i' $pilih>$i</option>";
                }
                ?>
            </select>

            <button type="submit" style="width: auto; padding: 10px;">Cek</button>
        </form>

        <?php
        include 'koneksi.php'; 

        // 1. Hitung Pemasukan (Sesuai Bulan & Tahun)
        $queryMasuk = "SELECT SUM(jumlah) AS total_masuk FROM transaksi 
                       WHERE jenis='Pemasukan' 
                       AND MONTH(tanggal)='$bulan_dipilih' 
                       AND YEAR(tanggal)='$tahun_dipilih'";
        $resultMasuk = mysqli_query($koneksi, $queryMasuk);
        $rowMasuk = mysqli_fetch_assoc($resultMasuk);
        $totalMasuk = $rowMasuk['total_masuk'] ?? 0; // Pakai 0 jika kosong

        // 2. Hitung Pengeluaran (Sesuai Bulan & Tahun)
        $queryKeluar = "SELECT SUM(jumlah) AS total_keluar FROM transaksi 
                        WHERE jenis='Pengeluaran' 
                        AND MONTH(tanggal)='$bulan_dipilih' 
                        AND YEAR(tanggal)='$tahun_dipilih'";
        $resultKeluar = mysqli_query($koneksi, $queryKeluar);
        $rowKeluar = mysqli_fetch_assoc($resultKeluar);
        $totalKeluar = $rowKeluar['total_keluar'] ?? 0; // Pakai 0 jika kosong

        // 3. Saldo Akhir
        $saldo = $totalMasuk - $totalKeluar;
        ?>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <div style="background: #2d1b4e; color: #00e676; padding: 15px; width: 33%; border: 4px solid #000;">
                <small>PEMASUKAN</small><br>
                <span style="font-size: 10px;">(Bulan Ini)</span><br>
                Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?>
            </div>
            <div style="background: #2d1b4e; color: #ff1744; padding: 15px; width: 33%; border: 4px solid #000;">
                <small>PENGELUARAN</small><br>
                <span style="font-size: 10px;">(Bulan Ini)</span><br>
                Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?>
            </div>
            <div style="background: #2d1b4e; color: #fff; padding: 15px; width: 33%; border: 4px solid #000;">
                <small>SISA SALDO</small><br>
                <span style="font-size: 10px;">(Bulan Ini)</span><br>
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
                // LOGIKA 3: TAMPILKAN DATA TABEL (DENGAN FILTER)
                $query = "SELECT * FROM transaksi 
                          WHERE MONTH(tanggal)='$bulan_dipilih' 
                          AND YEAR(tanggal)='$tahun_dipilih'
                          ORDER BY tanggal DESC"; // Diurutkan tanggal terbaru
                          
                $result = mysqli_query($koneksi, $query);

                // Cek jika data kosong
                if(mysqli_num_rows($result) == 0){
                    echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data di bulan ini.</td></tr>";
                }

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['tanggal'] . "</td>";
                    
                    $warna = ($row['jenis'] == 'Pemasukan') ? '#00e676' : '#ff1744';
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