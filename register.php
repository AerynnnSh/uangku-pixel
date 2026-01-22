<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun - Uangku</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="game-container" style="max-width: 400px; text-align: center;">
        
        <h2>Daftar Baru 🆕</h2>

        <?php
        include 'koneksi.php';
        
        if(isset($_POST['username'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // 1. Acak Password biar aman (Enkripsi)
            $password_encrypted = password_hash($password, PASSWORD_DEFAULT);

            // 2. Masukkan ke Database
            $query = "INSERT INTO users (username, password) VALUES ('$username', '$password_encrypted')";
            
            // 3. Cek Berhasil atau Gagal
            try {
                if(mysqli_query($koneksi, $query)) {
                    echo "<p style='color:green;'>✅ Berhasil Daftar! Silakan Login.</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color:red;'>❌ Username sudah dipakai!</p>";
            }
        }
        ?>

        <form action="" method="POST">
            <label>Username:</label>
            <input type="text" name="username" required autocomplete="off">
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit">DAFTAR SEKARANG</button>
            <br><br>
            <a href="login.php" style="color: #555; font-size: 10px;">Sudah punya akun? Login di sini</a>
        </form>
    </div>
</body>
</html>