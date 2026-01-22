<?php
session_start();
// 1. CEK LOGIN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php?message=please_login"); exit();
}
include 'koneksi.php';

// DATA USER
$u_user = $_SESSION['username'];
$dataUser = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE username='$u_user'"));

// Foto Profil
$profilePic = ($dataUser['foto'] && file_exists("images/".$dataUser['foto'])) ? "images/".$dataUser['foto'] : "https://ui-avatars.com/api/?name=".$u_user."&background=random&color=fff";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Finance Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Animasi kedip untuk peringatan budget */
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .blink-warning { color: var(--danger); animation: blink 1s infinite; font-weight: bold; }
    </style>
</head>
<body>

    <div class="game-container">
        
        <div class="rpg-box header-bar">
            <div style="display: flex; align-items: center;">
                <img src="<?php echo $profilePic; ?>" class="user-avatar">
                <div>
                    <h3 style="margin:0; color:var(--text-muted);">CURRENT USER</h3>
                    <span class="pixel-font" style="font-size:14px; color:var(--text-color);">
                        <?php echo strtoupper($u_user); ?>
                    </span>
                </div>
            </div>
            <div>
                <a href="profil.php" class="btn btn-small" style="background:#fff; color:#000;">⚙️ SETTINGS</a>
                <a href="logout.php" class="btn btn-small btn-danger">LOGOUT ➜</a>
            </div>
        </div>

        <?php
        // FILTER PARAMETERS
        $selectedMonth = $_GET['month'] ?? date('m');
        $selectedYear = $_GET['year'] ?? date('Y');
        $keyword = $_GET['keyword'] ?? '';

        // HITUNG TOTAL
        $qIncome = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(jumlah) AS t FROM transaksi WHERE jenis='Pemasukan' AND MONTH(tanggal)='$selectedMonth' AND YEAR(tanggal)='$selectedYear'"));
        $qExpense = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(jumlah) AS t FROM transaksi WHERE jenis='Pengeluaran' AND MONTH(tanggal)='$selectedMonth' AND YEAR(tanggal)='$selectedYear'"));
        
        $totalIncome = $qIncome['t'] ?? 0;
        $totalExpense = $qExpense['t'] ?? 0;
        $balance = $totalIncome - $totalExpense;

        // HITUNG BUDGET
        $budget = $dataUser['budget'];
        $percent = ($budget > 0) ? ($totalExpense / $budget) * 100 : 0;
        
        // Logika Over Budget
        $isOverBudget = ($budget > 0 && $totalExpense > $budget);
        
        $barColor = $isOverBudget ? 'hp-warning' : 'hp-bar-fill';
        $limitDisplay = ($percent >= 100) ? 100 : $percent;
        ?>

        <div class="dashboard-layout">
            
            <div class="left-panel">
                <div class="rpg-box">
                    <h2>+ ADD TRANSACTION</h2>
                    <form action="tambah.php" method="POST" id="transactionForm" novalidate>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <div>
                                <label>DATE</label>
                                <input type="date" name="tanggal" required max="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div>
                                <label>TYPE</label>
                                <select name="jenis">
                                    <option value="Pemasukan">INCOME (+)</option>
                                    <option value="Pengeluaran">EXPENSE (-)</option>
                                </select>
                            </div>
                        </div>
                        <label>CATEGORY</label>
                        <input type="text" name="kategori" placeholder="e.g. Food, Transport" required>
                        <label>AMOUNT</label>
                        <input type="number" name="jumlah" required>
                        <label>NOTE</label>
                        <textarea name="keterangan" rows="1"></textarea>
                        <button type="submit" class="btn">SAVE DATA</button>
                    </form>
                </div>

                <?php
                $qChart = mysqli_query($koneksi, "SELECT kategori, SUM(jumlah) as t FROM transaksi WHERE jenis='Pengeluaran' AND MONTH(tanggal)='$selectedMonth' AND YEAR(tanggal)='$selectedYear' GROUP BY kategori");
                $lbl=[]; $dat=[]; while($r=mysqli_fetch_assoc($qChart)){ $lbl[]=$r['kategori']; $dat[]=$r['t']; }
                ?>
                <?php if(count($lbl)>0): ?>
                <div class="rpg-box" style="text-align:center;">
                    <h2>EXPENSE BREAKDOWN</h2>
                    <canvas id="myChart"></canvas>
                </div>
                <script>
                    Chart.defaults.color = '#ccc';
                    Chart.defaults.font.family = "'VT323', monospace";
                    Chart.defaults.font.size = 14;
                    new Chart(document.getElementById('myChart'), {
                        type:'doughnut',
                        data:{
                            labels:<?=json_encode($lbl)?>, 
                            datasets:[{data:<?=json_encode($dat)?>, backgroundColor:['#e74c3c','#f1c40f','#3498db','#9b59b6','#2ecc71'], borderWidth:2, borderColor:'#1e1e24'}]
                        },
                        options:{plugins:{legend:{position:'bottom'}}}
                    });
                </script>
                <?php endif; ?>
            </div>

            <div class="right-panel">
                
                <div class="rpg-box">
                    <h2>FINANCIAL STATUS</h2>
                    <div class="stats-grid">
                        <div class="stat-item"><small>INCOME</small><span class="stat-val text-green"><?= number_format($totalIncome/1000, 1) ?>k</span></div>
                        <div class="stat-item"><small>EXPENSE</small><span class="stat-val text-red"><?= number_format($totalExpense/1000, 1) ?>k</span></div>
                        <div class="stat-item"><small>BALANCE</small><span class="stat-val text-yellow"><?= number_format($balance/1000, 1) ?>k</span></div>
                    </div>

                    <?php if($budget > 0): ?>
                    <div style="margin-top:15px;">
                        <div style="display:flex; justify-content:space-between; font-size:16px; color:var(--text-muted);">
                            <span>MONTHLY BUDGET</span>
                            <span class="<?= $isOverBudget ? 'blink-warning' : '' ?>">
                                <?= round($percent) ?>% <?= $isOverBudget ? '(OVER LIMIT!)' : '' ?>
                            </span>
                        </div>
                        <div class="hp-bar-container">
                            <div class="<?=$barColor?>" style="width: <?=$limitDisplay?>%;"></div>
                        </div>
                        <div style="text-align:right; font-size:14px; color:#555; margin-top:2px;">
                            Limit: <?= number_format($budget) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="rpg-box">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <h2>HISTORY</h2>
                        <a href="export.php?month=<?=$selectedMonth?>&year=<?=$selectedYear?>" class="btn btn-small" style="width:auto; background:#27ae60;">DOWNLOAD XLS</a>
                    </div>
                    
                    <form method="GET" style="display:flex; gap:5px; margin-bottom:15px;">
                        <select name="month" style="margin:0; width:auto;">
                            <?php for($i=1;$i<=12;$i++){ $b=str_pad($i,2,"0",STR_PAD_LEFT); $mn=date("F",mktime(0,0,0,$i,10)); $s=($selectedMonth==$b)?'selected':''; echo "<option value='$b' $s>$mn</option>"; } ?>
                        </select>
                        <select name="year" style="margin:0; width:auto;">
                            <?php $y=date('Y'); for($i=$y;$i>=$y-5;$i--){ $s=($selectedYear==$i)?'selected':''; echo "<option value='$i' $s>$i</option>"; } ?>
                        </select>
                        <button type="submit" class="btn btn-small" style="width:auto;">GO</button>
                    </form>

                    <?php
                    $perPage=6; $page=isset($_GET['page'])?(int)$_GET['page']:1; $start=($page>1)?($page*$perPage)-$perPage:0;
                    $sql="SELECT * FROM transaksi WHERE MONTH(tanggal)='$selectedMonth' AND YEAR(tanggal)='$selectedYear'";
                    if($keyword) $sql.=" AND kategori LIKE '%$keyword%'";
                    $totalRows=mysqli_num_rows(mysqli_query($koneksi, $sql)); $totalPages=ceil($totalRows/$perPage);
                    $q=mysqli_query($koneksi, $sql." ORDER BY tanggal DESC LIMIT $start, $perPage");
                    ?>

                    <div class="table-wrapper">
                        <table>
                            <thead><tr><th>DATE</th><th>CATEGORY</th><th>AMOUNT</th><th>ACT</th></tr></thead>
                            <tbody>
                                <?php if($totalRows == 0) echo "<tr><td colspan='4' align='center' style='padding:20px;color:#555'>No data.</td></tr>";
                                while($r=mysqli_fetch_assoc($q)): $clr=($r['jenis']=='Pemasukan')?'text-green':'text-red'; ?>
                                <tr>
                                    <td><?= date('d M', strtotime($r['tanggal'])) ?></td>
                                    <td><?= $r['kategori'] ?><br><small style="color:#555;font-size:14px"><?= ($r['jenis']=='Pemasukan'?'Income':'Expense') ?></small></td>
                                    <td class="<?=$clr?>"><?= number_format($r['jumlah']) ?></td>
                                    <td><a href="#" onclick="openModal('hapus.php?id=<?=$r['id']?>')" style="text-decoration:none;">🗑️</a></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($totalPages>1): ?>
                    <div style="margin-top:15px; text-align:center;">
                        <a href="?page=<?=max(1,$page-1)?>&month=<?=$selectedMonth?>&year=<?=$selectedYear?>" class="btn btn-small" style="width:30px;">&lt;</a>
                        <span style="font-family:'Press Start 2P'; font-size:10px; margin:0 10px; color:#555;">PAGE <?=$page?>/<?=$totalPages?></span>
                        <a href="?page=<?=min($totalPages,$page+1)?>&month=<?=$selectedMonth?>&year=<?=$selectedYear?>" class="btn btn-small" style="width:30px;">&gt;</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="confirmDelete">
        <div class="rpg-box modal-box">
            <h2 style="color:var(--danger)">⚠️ WARNING</h2>
            <p style="font-size:18px;">DELETE THIS TRANSACTION?</p>
            <div style="display:flex; gap:10px; justify-content:center; margin-top:20px;">
                <a id="btnYes" href="#" class="btn btn-danger">YES</a>
                <button onclick="closeModal()" class="btn" style="background:#444; color:#fff;">CANCEL</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="pixelAlert">
        <div class="rpg-box modal-box" style="border-color: var(--danger);">
            <h2 style="color:var(--danger); margin-bottom:10px;">🚫 ALERT</h2>
            <p id="pixelAlertMessage" style="font-size:18px; margin-bottom:20px; line-height: 1.4; white-space: pre-wrap;">MESSAGE</p>
            <button onclick="closePixelAlert()" class="btn">OK</button>
        </div>
    </div>

    <script>
        // Modal Logic
        const alertModal = document.getElementById('pixelAlert');
        const alertMsg = document.getElementById('pixelAlertMessage');
        function showPixelAlert(msg) { alertMsg.innerText = msg; alertModal.style.display = 'flex'; }
        function closePixelAlert() { alertModal.style.display = 'none'; }
        function openModal(url){ document.getElementById('confirmDelete').style.display='flex'; document.getElementById('btnYes').href=url; }
        function closeModal(){ document.getElementById('confirmDelete').style.display='none'; }
        window.onclick=function(e){ 
            if(e.target==document.getElementById('confirmDelete')) closeModal();
            if(e.target==alertModal) closePixelAlert();
        }

        // Form Validation Logic
        const form = document.getElementById('transactionForm');
        form.addEventListener('submit', function(event) {
            const d = form.querySelector('input[name="tanggal"]').value;
            const c = form.querySelector('input[name="kategori"]').value;
            const a = form.querySelector('input[name="jumlah"]').value;
            if(!d || !c || !a) { event.preventDefault(); showPixelAlert("PLEASE FILL ALL REQUIRED FIELDS!"); return; }
            if(new Date(d) > new Date().setHours(0,0,0,0)) { event.preventDefault(); showPixelAlert("TIME TRAVEL DETECTED!\nCANNOT ENTER FUTURE DATE."); return; }
        });
    </script>

    <?php if($isOverBudget): ?>
    <script>
        // Script ini otomatis jalan kalau budget jebol
        window.addEventListener('load', function() {
            showPixelAlert("⚠️ CRITICAL WARNING! \n\nMONTHLY BUDGET EXCEEDED!\nLimit: <?= number_format($budget) ?>");
        });
    </script>
    <?php endif; ?>

    <script src="transition.js"></script>

</body>
</html>