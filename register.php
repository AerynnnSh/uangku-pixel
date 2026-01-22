<?php
include 'koneksi.php';
$msg = "";
$msgType = ""; // 'success' or 'error'

if(isset($_POST['username'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Enkripsi Password
    $password_encrypted = password_hash($password, PASSWORD_DEFAULT);

    // Cek Username Duplicate
    try {
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$password_encrypted')";
        if(mysqli_query($koneksi, $query)) {
            $msg = "REGISTRATION SUCCESSFUL! Redirecting...";
            $msgType = "success";
            header("refresh:2;url=login.php"); // Auto pindah ke login setelah 2 detik
        }
    } catch (Exception $e) {
        $msg = "USERNAME ALREADY TAKEN! Choose another.";
        $msgType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register New User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-wrapper {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        
        <?php if($msg != ""): ?>
        <div class="rpg-box" style="border-color: <?php echo ($msgType=='success') ? 'var(--success)' : 'var(--danger)'; ?>; text-align: center; padding: 10px; margin-bottom: 20px;">
            <small style="color: <?php echo ($msgType=='success') ? 'var(--success)' : 'var(--danger)'; ?>; font-family: 'Press Start 2P';">
                <?php echo ($msgType=='success') ? '✅ SUCCESS' : '🚫 ERROR'; ?>
            </small>
            <p style="margin: 5px 0 0 0; color: #fff;"><?php echo $msg; ?></p>
        </div>
        <?php endif; ?>

        <div class="rpg-box">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2 style="margin-bottom: 5px; color: var(--success);">NEW USER</h2>
                <small style="color: #666; font-size: 14px;">CREATE DATABASE ENTRY</small>
            </div>
            
            <form action="" method="POST">
                <label>CHOOSE USERNAME</label>
                <input type="text" name="username" required placeholder="Ex: PlayerOne" autocomplete="off">
                
                <label>SET PASSWORD</label>
                <input type="password" name="password" required placeholder="******">
                
                <button type="submit" class="btn btn-success" style="margin-top: 10px; color: #000;">
                    CREATE ACCOUNT ✚
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px; border-top: 1px solid #333; padding-top: 15px;">
                <span style="color: #666; font-size: 16px;">Already have access?</span><br>
                <a href="login.php" style="color: var(--primary-btn); text-decoration: none; font-size: 18px;">
                    [ BACK TO LOGIN ]
                </a>
            </div>
        </div>

    </div>

    <script src="transition.js"></script>

</body>
</html>