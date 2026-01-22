<?php
session_start(); // Mulai sesi (Siapkan gelang)

// Kalau sudah login, lempar langsung ke index
if(isset($_SESSION['status']) && $_SESSION['status'] == "login"){
    header("location:index.php");
}

include 'koneksi.php';

// LOGIKA LOGIN
if(isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Cari user berdasarkan username
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);
    $cek = mysqli_num_rows($result);

    if($cek > 0){
        $data = mysqli_fetch_assoc($result);
        
        // 2. Cek apakah password cocok dengan yang di database?
        if(password_verify($password, $data['password'])){
            // SUKSES! Berikan "Gelang Konser" (Session)
            $_SESSION['username'] = $username;
            $_SESSION['status'] = "login";
            
            header("location:index.php");
        } else {
            $error = "Password Salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Uangku</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="game-container" style="max-width: 400px; text-align: center; margin-top: 50px;">
        
        <h2>LOGIN AREA 🔐</h2>

        <?php if(isset($error)): ?>
            <p style="color: red; background: #ffcccb; padding: 10px; border: 2px solid red;">
                ⚠️ <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form action="" method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit" class="btn">START GAME (MASUK)</button>
            <br><br>
            <a href="register.php" style="color: #555; font-size: 10px;">Belum punya akun? Daftar dulu</a>
        </form>
    </div>
</body>
</html>