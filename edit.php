<?php
include 'koneksi.php';
$id = $_GET['id'];
$query = "SELECT * FROM transaksi WHERE id='$id'";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Transaksi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <div class="game-container">
        <h2>Edit Data ✏️</h2>

        <form action="update.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

            <label>Tanggal:</label>
            <input type="date" name="tanggal" value="<?php echo $row['tanggal']; ?>">
            
            <label>Jenis:</label>
            <select name="jenis">
                <option value="Pemasukan" <?php if($row['jenis']=='Pemasukan') echo 'selected'; ?>>Pemasukan (+)</option>
                <option value="Pengeluaran" <?php if($row['jenis']=='Pengeluaran') echo 'selected'; ?>>Pengeluaran (-)</option>
            </select>
            
            <label>Kategori:</label>
            <input type="text" name="kategori" value="<?php echo $row['kategori']; ?>">
            
            <label>Jumlah (Rp):</label>
            <input type="number" name="jumlah" value="<?php echo $row['jumlah']; ?>">
            
            <label>Keterangan:</label>
            <textarea name="keterangan"><?php echo $row['keterangan']; ?></textarea>
            
            <button type="submit">Update Perubahan</button>
            <br><br>
            <a href="index.php" style="display:block; text-align:center; color: #555;">Batal</a>
        </form>
    </div>

</body>
</html>