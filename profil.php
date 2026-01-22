<?php
session_start();
include 'koneksi.php';

// CEK LOGIN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php"); exit();
}

$username = $_SESSION['username'];

// --- LOGIKA UPDATE ---
if(isset($_POST['simpan'])) {
    $budget = $_POST['budget'];
    
    // Logika Upload Foto
    $namaFile = $_FILES['foto']['name'];
    $tmpName = $_FILES['foto']['tmp_name'];
    
    if($namaFile != "") {
        $ekstensiValid = ['png', 'jpg', 'jpeg'];
        $ekstensi = explode('.', $namaFile);
        $ekstensi = strtolower(end($ekstensi));

        if(in_array($ekstensi, $ekstensiValid)) {
            $namaBaru = $username . "_" . uniqid() . "." . $ekstensi;
            move_uploaded_file($tmpName, 'images/' . $namaBaru);
            $query = "UPDATE users SET budget='$budget', foto='$namaBaru' WHERE username='$username'";
        } else {
            echo "<script>alert('Invalid file format! Please use JPG/PNG.');</script>";
        }
    } else {
        $query = "UPDATE users SET budget='$budget' WHERE username='$username'";
    }
    
    mysqli_query($koneksi, $query);
    header("location:index.php"); 
}

// AMBIL DATA USER
$dataUser = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'"));

// Gambar Preview
$foto = $dataUser['foto'];
if($foto == "" || !file_exists("images/$foto")) $foto = "https://ui-avatars.com/api/?name=".$username."&background=random&color=fff"; 
else $foto = "images/" . $foto;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS KHUSUS HALAMAN INI */
        .settings-container { max-width: 500px; margin: 40px auto; }
        
        .profile-preview {
            width: 100px; height: 100px; 
            border-radius: 4px; /* Kotak sedikit rounded ala pixel */
            border: 2px solid #fff; 
            object-fit: cover;
            margin-bottom: 10px;
            box-shadow: 4px 4px 0 #000;
        }

        /* CUSTOM FILE INPUT (Pixel Style) */
        .file-upload-wrapper {
            position: relative;
            margin-bottom: 20px;
            text-align: center;
        }
        
        /* Sembunyikan input asli yang jelek */
        input[type="file"] {
            display: none; 
        }

        /* Label ini akan jadi tombol pengganti */
        .custom-file-upload {
            display: inline-block;
            background: #444;
            color: #fff;
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #777;
            font-family: 'Press Start 2P', cursive;
            font-size: 10px;
            box-shadow: 4px 4px 0 #000;
            width: 100%;
        }
        .custom-file-upload:active { transform: translate(2px, 2px); box-shadow: 2px 2px 0 #000; }
        
        #fileNameDisplay {
            display: block;
            margin-top: 10px;
            font-size: 16px;
            color: var(--accent);
        }

        /* Helper Text yang lebih jelas */
        .helper-text {
            font-size: 16px; /* Diperbesar dari 8px */
            color: #888;
            margin-top: -10px;
            margin-bottom: 20px;
            display: block;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <div class="game-container settings-container">
        
        <div class="rpg-box">
            <h2 style="text-align:center;">⚙️ USER SETTINGS</h2>
            <hr style="border-color:#333; margin-bottom:30px;">

            <form action="" method="POST" enctype="multipart/form-data">
                
                <div style="text-align: center; margin-bottom: 30px;">
                    <img src="<?php echo $foto; ?>" class="profile-preview">
                    
                    <div class="file-upload-wrapper">
                        <label for="fileInput" class="custom-file-upload">
                            📂 CHOOSE NEW AVATAR
                        </label>
                        <input type="file" name="foto" id="fileInput" onchange="updateFileName()">
                        
                        <span id="fileNameDisplay">No file chosen</span>
                    </div>
                </div>

                <label>MONTHLY BUDGET TARGET (Rp)</label>
                <input type="number" name="budget" value="<?php echo $dataUser['budget']; ?>">
                <small class="helper-text">
                    ℹ️ Set to <strong>0</strong> to disable the budget alert system.
                </small>

                <button type="submit" name="simpan" class="btn" style="background:var(--primary-btn); margin-bottom:15px;">
                    SAVE CHANGES 💾
                </button>
                
                <a href="index.php" class="btn btn-danger">
                    CANCEL / BACK ➜
                </a>

            </form>
        </div>

    </div>

    <script>
        function updateFileName() {
            const input = document.getElementById('fileInput');
            const display = document.getElementById('fileNameDisplay');
            if (input.files.length > 0) {
                display.innerText = "Selected: " + input.files[0].name;
                display.style.color = "var(--success)";
            } else {
                display.innerText = "No file chosen";
                display.style.color = "var(--accent)";
            }
        }
    </script>

    <script src="transition.js"></script>

</body>
</html>