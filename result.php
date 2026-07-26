<?php
require 'config.php';
if (!isLoggedIn()) redirect('index.php');
 $userId = $_SESSION['user_id'];
 $username = $_SESSION['username'];

 $stmt = $pdo->prepare("SELECT * FROM scores WHERE user_id = ? ORDER BY played_at DESC");
 $stmt->execute([$userId]);
 $scores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Nilai - Tebak Jepang</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ec4899; --primary-dark: #be185d; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #fce7f3 0%, #fdf2f8 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; color: #831843; }
        .card { background: #fff; padding: 40px; border-radius: 24px; box-shadow: 0 20px 50px rgba(236, 72, 153, 0.15); width: 100%; max-width: 650px; position: relative; overflow: hidden; }
        .card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 8px; background: linear-gradient(90deg, var(--primary), #d946ef); }
        h1 { color: var(--primary-dark); text-align: center; margin-bottom: 5px; }
        p.subtitle { text-align: center; color: #db2777; margin-bottom: 30px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px 10px; text-align: center; border-bottom: 1px solid #fce7f3; font-size: 0.9rem; }
        th { background: #fce7f3; color: var(--primary-dark); }
        tr:hover { background: #fff0f8; }
        .empty { text-align: center; color: #64748b; padding: 30px; }
        .btn { display: inline-block; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: 0.3s; }
        .btn-success { background: #10b981; color: #fff; }
        .btn-success:hover { background: #059669; }
        .btn-container { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
        .btn-big { flex: 1; padding: 12px; text-align: center; border-radius: 10px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .btn-big.primary { background: var(--primary); color: #fff; }
        .btn-big.secondary { background: #fce7f3; color: var(--primary-dark); }
        .copyright { margin-top: 20px; font-size: 0.75rem; color: #db2777; opacity: 0.7; font-weight: 600; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <h1>📊 Riwayat Nilai</h1>
        <p class="subtitle">User: <?= htmlspecialchars($username) ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelompok</th>
                    <th>Nilai</th>
                    <th>Tanggal Main</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($scores)): ?>
                    <tr><td colspan="5" class="empty">Belum ada riwayat permainan. Ayo main!</td></tr>
                <?php else: $no = 1; foreach($scores as $s): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span style="background:#fce7f3; padding:4px 10px; border-radius:20px; color:var(--primary-dark); font-weight:700;">Kelompok <?= $s['group_type'] ?></span></td>
                        <td style="font-weight:800; color: var(--primary-dark);"><?= $s['score'] ?>/<?= $s['total'] ?></td>
                        <td style="font-size: 0.85rem; color: #64748b;"><?= date('d M Y H:i', strtotime($s['played_at'])) ?></td>
                        <td>
                            <a href="detail.php?id=<?= $s['id'] ?>" class="btn btn-success">Lihat Salah</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>

        <div class="btn-container">
            <a href="quiz.php" class="btn-big primary">Main Lagi</a>
            <a href="logout.php" class="btn-big secondary">Keluar</a>
        </div>
        <div class="copyright">© Copyright by sultbabies</div>
    </div>
</body>
</html>