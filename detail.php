<?php
require 'config.php';
if (!isLoggedIn()) redirect('index.php');

 $scoreId = $_GET['id'] ?? 0;
 $userId = $_SESSION['user_id'];

 $stmt = $pdo->prepare("SELECT * FROM scores WHERE id = ? AND user_id = ?");
 $stmt->execute([$scoreId, $userId]);
 $scoreData = $stmt->fetch();

if (!$scoreData) {
    redirect('result.php');
}

 $stmtWrong = $pdo->prepare("SELECT * FROM wrong_answers WHERE score_id = ?");
 $stmtWrong->execute([$scoreId]);
 $wrongAnswers = $stmtWrong->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembahasan Jawaban - Tebak Jepang</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ec4899; --primary-dark: #be185d; --danger: #ef4444; --success: #10b981; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(135deg, #fce7f3 0%, #fdf2f8 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; color: #831843; }
        .card { background: #fff; padding: 40px; border-radius: 24px; box-shadow: 0 20px 50px rgba(236, 72, 153, 0.15); width: 100%; max-width: 600px; position: relative; overflow: hidden; }
        .card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 8px; background: linear-gradient(90deg, var(--primary), #d946ef); }
        h1 { color: var(--primary-dark); text-align: center; margin-bottom: 5px; }
        p.subtitle { text-align: center; color: #db2777; margin-bottom: 15px; font-size: 0.9rem; }
        .group-badge { display: inline-block; background: #fce7f3; color: var(--primary-dark); padding: 6px 15px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; margin-bottom: 20px; }
        .score-banner { background: #fce7f3; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 25px; font-weight: 800; font-size: 1.2rem; color: var(--primary-dark); }
        .review-item { background: #fff; border: 2px solid #fee2e2; border-radius: 12px; padding: 15px; margin-bottom: 15px; }
        .question-row { font-weight: 600; margin-bottom: 10px; color: #831843; }
        .answer-row { display: flex; justify-content: space-between; padding: 8px 0; border-top: 1px dashed #fbcfe8; font-family: 'Noto Sans JP', sans-serif; }
        .wrong-text { color: var(--danger); font-weight: 700; text-decoration: line-through; }
        .correct-text { color: var(--success); font-weight: 700; }
        .label-tag { font-size: 0.75rem; color: #64748b; font-family: 'Poppins', sans-serif; display: block; margin-bottom: 3px; }
        .empty { text-align: center; padding: 30px; background: #f0fdf4; border-radius: 12px; border: 1px solid #bbf7d0; color: #10b981; font-weight: 600; }
        .btn-back { display: block; width: 100%; padding: 12px; background: var(--primary); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; text-decoration: none; text-align: center; margin-top: 10px; transition: 0.3s; }
        .btn-back:hover { background: var(--primary-dark); }
        .copyright { margin-top: 20px; font-size: 0.75rem; color: #db2777; opacity: 0.7; font-weight: 600; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <h1>📝 Pembahasan Jawaban</h1>
        <div style="text-align: center;">
            <span class="group-badge">Kelompok <?= htmlspecialchars($scoreData['group_type']) ?></span>
        </div>
        <p class="subtitle">Tanggal Main: <?= date('d M Y H:i', strtotime($scoreData['played_at'])) ?></p>
        
        <div class="score-banner">
            Nilai Kamu: <?= $scoreData['score'] ?> / <?= $scoreData['total'] ?>
        </div>

        <?php if (empty($wrongAnswers)): ?>
            <div class="empty">
                🎉 Sempurna! Kamu tidak menjawab salah sama sekali!
            </div>
        <?php else: ?>
            <?php $no = 1; foreach ($wrongAnswers as $wa): ?>
                <div class="review-item">
                    <div class="question-row"><?= $no++ ?>. <?= htmlspecialchars($wa['question_text']) ?></div>
                    <div class="answer-row">
                        <div>
                            <span class="label-tag">Jawaban Kamu (Salah)</span>
                            <span class="wrong-text"><?= htmlspecialchars($wa['user_answer']) ?></span>
                        </div>
                        <div style="text-align: right;">
                            <span class="label-tag">Jawaban Benar</span>
                            <span class="correct-text"><?= htmlspecialchars($wa['correct_answer']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="result.php" class="btn-back">Kembali ke Riwayat</a>
        <div class="copyright">© Copyright by sultbabies</div>
    </div>
</body>
</html>