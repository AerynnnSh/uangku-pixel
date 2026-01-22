<?php
session_start();

// 1. CEK SATPAM: Kalau belum login, tendang ke login.php
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Uangku - RPG Edition</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="game-container">
        
        <div class="header-bar">
            <div>
                <small>PLAYER 1</small><br>
                <span style="color: var(--primary);">👤 <?php echo strtoupper($_SESSION['username']); ?></span>
            </div>
            <a href="logout.php" class="btn btn-danger btn-small">
                QUIT GAME 🚪
            </a>
        </div>

        <?php
        // Ambil filter bulan/tahun dari URL (default: sekarang)
        $bulan_dipilih = $_GET['bulan'] ?? date('m');
        $tahun_dipilih = $_GET['tahun'] ?? date('Y');
        ?>

        <h2>⚔️ DASHBOARD KEUANGAN</h2>

        <?php
        include 'koneksi.php'; 

        // HITUNG PEMASUKAN (LOOT)
        $queryMasuk = "SELECT SUM(jumlah) AS total_masuk FROM transaksi 
                       WHERE jenis='Pemasukan' 
                       AND MONTH(tanggal)='$bulan_dipilih' 
                       AND YEAR(tanggal)='$tahun_dipilih'";
        $rowMasuk = mysqli_fetch_assoc(mysqli_query($koneksi, $queryMasuk));
        $totalMasuk = $rowMasuk['total_masuk'] ?? 0;

        // HITUNG PENGELUARAN (DAMAGE)
        $queryKeluar = "SELECT SUM(jumlah) AS total_keluar FROM transaksi 
                        WHERE jenis='Pengeluaran' 
                        AND MONTH(tanggal)='$bulan_dipilih' 
                        AND YEAR(tanggal)='$tahun_dipilih'";
        $rowKeluar = mysqli_fetch_assoc(mysqli_query($koneksi, $queryKeluar));
        $totalKeluar = $rowKeluar['total_keluar'] ?? 0;

        // HITUNG SALDO (HP)
        $saldo = $totalMasuk - $totalKeluar;
        ?>

        <div class="dashboard-grid">
            <div class="card">
                <small>LOOT (PEMASUKAN)</small>
                <span class="nominal text-green">Rp <?php echo number_format($totalMasuk, 0, ',', '.'); ?></span>
            </div>
            <div class="card">
                <small>DAMAGE (PENGELUARAN)</small>
                <span class="nominal text-red">Rp <?php echo number_format($totalKeluar, 0, ',', '.'); ?></span>
            </div>
            <div class="card">
                <small>HP (SISA SALDO)</small>
                <span class="nominal text-white">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></span>
            </div>
        </div>

        <form action="" method="GET" class="filter-box">
            <label style="margin:0;">QUEST LOG (FILTER):</label>
            <select name="bulan">
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
            <select name="tahun">
                <?php
                $tahun_sekarang = date('Y');
                for($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--){
                    $pilih = ($tahun_dipilih == $i) ? 'selected' : '';
                    echo "<option value='$i' $pilih>$i</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn-success" style="width: auto;">LOAD</button>
        </form>

        <hr>

        <h3>📜 NEW QUEST (INPUT)</h3>
        <form action="tambah.php" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>TANGGAL</label>
                    <input type="date" name="tanggal" required>
                </div>
                <div>
                    <label>TIPE</label>
                    <select name="jenis">
                        <option value="Pemasukan">Pemasukan (+)</option>
                        <option value="Pengeluaran">Pengeluaran (-)</option>
                    </select>
                </div>
            </div>
            
            <label>KATEGORI (ITEM)</label>
            <input type="text" name="kategori" placeholder="Contoh: Potion, Sword, Food" required>
            
            <label>JUMLAH GOLD (Rp)</label>
            <input type="number" name="jumlah" required>
            
            <label>CATATAN</label>
            <textarea name="keterangan" rows="2"></textarea>
            
            <button type="submit" class="btn">SAVE GAME (SIMPAN)</button>
        </form>

        <br>

        <?php
        $queryChart = "SELECT kategori, SUM(jumlah) AS total FROM transaksi 
                       WHERE jenis='Pengeluaran' 
                       AND MONTH(tanggal)='$bulan_dipilih' 
                       AND YEAR(tanggal)='$tahun_dipilih' 
                       GROUP BY kategori";
        $resultChart = mysqli_query($koneksi, $queryChart);
        
        $namaKategori = []; 
        $totalPerKategori = [];
        
        while($row = mysqli_fetch_assoc($resultChart)) {
            $namaKategori[] = $row['kategori']; 
            $totalPerKategori[] = $row['total'];
        }
        ?>

        <?php if(count($namaKategori) > 0): ?>
            <div style="background: #fff; border: 4px solid #000; padding: 20px; margin-bottom: 30px; text-align: center;">
                <h3>📊 STATISTIK</h3>
                <div style="max-width: 400px; margin: 0 auto;">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
            <script>
                const ctx = document.getElementById('myChart');
                Chart.defaults.font.family = "'Press Start 2P', cursive";
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode($namaKategori); ?>,
                        datasets: [{
                            data: <?php echo json_encode($totalPerKategori); ?>,
                            backgroundColor: ['#d95763', '#fbf236', '#639bff', '#ac3232', '#99e550'],
                            borderWidth: 2, 
                            borderColor: '#000'
                        }]
                    }
                });
            </script>
        <?php endif; ?>

        <h3>📜 HISTORY LOG</h3>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>DATE</th>
                        <th>TYPE</th>
                        <th>ITEM</th>
                        <th>GOLD</th>
                        <th>NOTE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM transaksi 
                              WHERE MONTH(tanggal)='$bulan_dipilih' 
                              AND YEAR(tanggal)='$tahun_dipilih' 
                              ORDER BY tanggal DESC";
                    $result = mysqli_query($koneksi, $query);

                    if(mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='6' style='text-align:center; padding: 30px;'>NO DATA FOUND...</td></tr>";
                    }

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['tanggal'] . "</td>";
                        
                        $warna = ($row['jenis'] == 'Pemasukan') ? 'var(--success)' : 'var(--danger)';
                        echo "<td style='color:$warna; font-weight:bold;'>" . $row['jenis'] . "</td>";
                        
                        echo "<td>" . $row['kategori'] . "</td>";
                        echo "<td>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                        echo "<td>" . $row['keterangan'] . "</td>";
                        
                        // Perhatikan bagian tombol DEL di bawah ini:
                        // Kita memanggil fungsi bukaModal() dan mengirim Link Hapus sebagai parameter
                        echo "<td>
                                <a href='edit.php?id=" . $row['id'] . "' class='btn btn-small' style='background:#639bff;'>EDIT</a>
                                <a href='#' onclick=\"bukaModal('hapus.php?id=" . $row['id'] . "')\" class='btn btn-small btn-danger'>DEL</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div> <div class="modal-overlay" id="konfirmasiHapus">
        <div class="modal-box">
            <h3>⚠️ WARNING!</h3>
            <p>Yakin mau menghapus data ini? Item yang hilang tidak bisa dikembalikan (Permanen).</p>
            
            <div class="modal-buttons">
                <a id="btnYa" href="#" class="btn btn-danger">YES, DELETE</a>
                
                <button onclick="tutupModal()" class="btn" style="background: #999; color: #000;">CANCEL</button>
            </div>
        </div>
    </div>

    <script>
        function bukaModal(urlHapus) {
            document.getElementById('konfirmasiHapus').style.display = 'flex';
            document.getElementById('btnYa').href = urlHapus;
        }

        function tutupModal() {
            document.getElementById('konfirmasiHapus').style.display = 'none';
        }

        // Tutup modal kalau klik di luar kotak
        window.onclick = function(event) {
            let modal = document.getElementById('konfirmasiHapus');
            if (event.target == modal) {
                tutupModal();
            }
        }
    </script>

</body>
</html>