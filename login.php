<?php
session_start();
include 'koneksi.php';

// Redirect if already logged in
if(isset($_SESSION['status']) && $_SESSION['status'] == "login"){
    header("location:index.php"); exit();
}

$errorMsg = "";

// LOGIKA LOGIN
if(isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);
    $cek = mysqli_num_rows($result);

    if($cek > 0){
        $data = mysqli_fetch_assoc($result);
        if(password_verify($password, $data['password'])){
            // SUKSES LOGIN
            $_SESSION['username'] = $username;
            $_SESSION['status'] = "login";
            header("location:index.php");
        } else {
            $errorMsg = "ACCESS DENIED: Wrong Password.";
        }
    } else {
        $errorMsg = "USER NOT FOUND: Check your username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="style.css">
    <style>
        /* Center content vertically & horizontally */
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
        
        <?php if($errorMsg != ""): ?>
        <div class="rpg-box" style="border-color: var(--danger); text-align: center; padding: 10px; margin-bottom: 20px;">
            <small style="color: var(--danger); font-family: 'Press Start 2P';">🚫 ERROR</small>
            <p style="margin: 5px 0 0 0; color: #fff;"><?php echo $errorMsg; ?></p>
        </div>
        <?php endif; ?>

        <div class="rpg-box">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2 style="margin-bottom: 5px; color: var(--primary-btn);">SYSTEM LOGIN</h2>
                <small style="color: #666; font-size: 14px;">SECURE TERMINAL ACCESS v2.0</small>
            </div>
            
            <form action="" method="POST">
                <label>USERNAME / ID</label>
                <input type="text" name="username" required placeholder="Enter username..." autocomplete="off">
                
                <label>PASSWORD</label>
                <input type="password" name="password" required placeholder="Enter password...">
                
                <button type="submit" class="btn" style="margin-top: 10px;">
                    AUTHENTICATE ➜
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px; border-top: 1px solid #333; padding-top: 15px;">
                <span style="color: #666; font-size: 16px;">New User?</span><br>
                <a href="register.php" style="color: var(--accent); text-decoration: none; font-size: 18px;">
                    [ INITIALIZE REGISTRATION ]
                </a>
            </div>
        </div>

    </div>

    <script src="transition.js"></script>       

</body>
</html>