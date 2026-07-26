<?php
require 'config.php';
if (!isLoggedIn()) redirect('index.php');
 $user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Tebak Kata Kerja Jepang</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ec4899; --primary-dark: #be185d; --secondary: #d946ef; --success: #10b981; --danger: #ef4444; --text: #831843; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #fce7f3 0%, #fdf2f8 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; color: var(--text); }
        .container { background: #fff; border-radius: 24px; box-shadow: 0 20px 50px rgba(236, 72, 153, 0.15); width: 100%; max-width: 500px; padding: 40px 30px; text-align: center; position: relative; overflow: hidden; }
        .container::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 8px; background: linear-gradient(90deg, var(--primary), var(--secondary)); }
        .header-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 0.9rem; }
        .user-badge { background: #fce7f3; padding: 8px 15px; border-radius: 20px; color: var(--primary-dark); font-weight: 600; }
        .nav-link { color: var(--primary-dark); text-decoration: none; font-weight: 600; margin-left: 10px; }
        h1 { font-size: 1.8rem; margin-bottom: 10px; color: var(--primary-dark); }
        .subtitle { font-size: 0.9rem; color: #db2777; margin-bottom: 30px; }
        
        /* Layout Score & Timer Baru */
        .score-board { display: flex; justify-content: space-between; background: #fce7f3; padding: 15px 20px; border-radius: 12px; margin-bottom: 15px; font-weight: 600; color: var(--primary-dark); }
        .score-board span { color: var(--primary); font-weight: 800; }
        
        .timer-bar { background: var(--primary); color: white; padding: 15px; border-radius: 12px; margin-bottom: 25px; font-size: 1.8rem; font-weight: 800; transition: all 0.3s ease; }
        .timer-bar.warning { background: var(--danger); animation: pulse 1s infinite; }
        
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
        
        .question-box { margin-bottom: 30px; }
        .question-label { font-size: 0.9rem; color: #db2777; margin-bottom: 10px; }
        .question-text { font-size: 2rem; font-weight: 700; color: var(--primary-dark); padding: 20px; background: #fce7f3; border-radius: 16px; border: 2px dashed #f9a8d4; word-wrap: break-word; }
        .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .option-btn { background: #fff; border: 2px solid #fbcfe8; padding: 18px 10px; border-radius: 12px; font-family: 'Noto Sans JP', sans-serif; font-size: clamp(1rem, 4vw, 1.4rem); font-weight: 700; color: var(--text); cursor: pointer; transition: all 0.2s ease; word-break: break-all; }
        .option-btn:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(236, 72, 153, 0.2); background: #fff0f8; }
        .option-btn.correct { background: var(--success); border-color: var(--success); color: #fff; animation: pop 0.3s ease; }
        .option-btn.wrong { background: var(--danger); border-color: var(--danger); color: #fff; animation: shake 0.4s ease; }
        @keyframes pop { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(131, 24, 67, 0.6); backdrop-filter: blur(5px); display: none; justify-content: center; align-items: center; z-index: 100; animation: fadeIn 0.3s ease; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #fff; padding: 40px 30px; border-radius: 20px; text-align: center; max-width: 380px; width: 90%; animation: slideUp 0.4s ease; border-top: 8px solid var(--danger); }
        .modal-icon { font-size: 3.5rem; margin-bottom: 15px; }
        .modal-title { font-size: 1.5rem; font-weight: 800; color: var(--danger); margin-bottom: 10px; }
        .modal-text { font-size: 1rem; color: #64748b; margin-bottom: 25px; line-height: 1.5; }
        .modal-text b { font-family: 'Noto Sans JP', sans-serif; color: var(--primary-dark); font-size: 1.2rem; }
        .modal-btn { background: var(--primary); color: #fff; border: none; padding: 12px 30px; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 10px rgba(236, 72, 153, 0.3); text-decoration: none; display: inline-block; margin: 5px; }
        .modal-btn:hover { background: var(--primary-dark); transform: translateY(-2px); }
        .modal-btn.success { background: var(--success); box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); }
        .modal-btn.success:hover { background: #059669; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-nav">
            <div class="user-badge">👤 <?= htmlspecialchars($user) ?></div>
            <div>
                <a href="result.php" class="nav-link">Lihat Nilai</a>
                <a href="logout.php" class="nav-link">Keluar</a>
            </div>
        </div>

        <h1>🌸 Tebak Kata Kerja</h1>
        <p class="subtitle">Tebak kosakata Bahasa Jepang (10 Soal)</p>

        <div class="score-board">
            <div>Benar: <span id="score">0</span></div>
            <div>Soal: <span id="question-num">1</span> / 10</div>
        </div>

        <div class="timer-bar" id="timer-bar">⏱ <span id="timer">15</span> detik</div>

        <div class="question-box">
            <p class="question-label">Terjemahan dari kata:</p>
            <div class="question-text" id="question">Makan</div>
        </div>
        <div class="options-grid" id="options"></div>
    </div>

    <div class="modal-overlay" id="alertModal">
        <div class="modal-box">
            <div class="modal-icon" id="alertIcon">❌</div>
            <h2 class="modal-title" id="alertTitle">Jawaban Salah!</h2>
            <p class="modal-text">Jawaban yang benar adalah:<br><b id="correctAnswer"></b></p>
            <button class="modal-btn" id="retryBtn">Lanjut</button>
        </div>
    </div>

    <div class="modal-overlay" id="finishModal">
        <div class="modal-box" style="border-top-color: var(--success);">
            <div class="modal-icon">🎉</div>
            <h2 class="modal-title" style="color: var(--success);">Permainan Selesai!</h2>
            <p class="modal-text">Nilai kamu: <br><b id="finalScore" style="font-size: 2rem; color: var(--primary-dark);"></b></p>
            <a href="quiz.php" class="modal-btn">Main Lagi</a>
            <a href="result.php" class="modal-btn success">Lihat Pembahasan</a>
        </div>
    </div>

    <script>
        const vocabData = [
            { id: "bertemu, berjumpa", jp: "あいます" }, { id: "bermain", jp: "あそびます" }, { id: "mencuci (tangan)", jp: "あらいます" }, { id: "ada", jp: "あります" }, { id: "berjalan kaki", jp: "あるきます" }, { id: "berkata, mengatakan", jp: "いいます" }, { id: "pergi", jp: "いきます" }, { id: "terburu-buru", jp: "いそぎます" }, { id: "memerlukan", jp: "いります" }, { id: "bergerak, berputar", jp: "うごきます" }, { id: "menyanyi", jp: "うたいます" }, { id: "menjual", jp: "うります" }, { id: "meletakkan", jp: "おきます" }, { id: "mengirim, mengantar", jp: "おくります" }, { id: "menekan, mendorong", jp: "おします" }, { id: "mengira, merasa", jp: "おもいます" }, { id: "teringat", jp: "おもいだします" }, { id: "berenang", jp: "およぎます" }, { id: "habis, selesai", jp: "おわります" }, { id: "membeli", jp: "かいます" }, { id: "mengembalikan", jp: "かえします" }, { id: "pulang, kembali", jp: "かえります" }, { id: "memerlukan (waktu)", jp: "かかります" }, { id: "menulis, menggambar", jp: "かきます" }, { id: "meminjamkan", jp: "かします" }, { id: "menang", jp: "かちます" }, { id: "memakai (topi)", jp: "かぶります" }, { id: "mendengar, bertanya", jp: "ききます" }, { id: "memotong, menggunting", jp: "きります" }, { id: "mematikan, memadamkan", jp: "けします" }, { id: "menyentuh, memegang", jp: "さわります" }, { id: "tahu, mengetahui", jp: "しります" }, { id: "merokok, menghisap", jp: "すいます" }, { id: "tinggal, bermukim", jp: "すみます" }, { id: "duduk", jp: "すわります" }, { id: "berdiri", jp: "たちます" }, { id: "mengeluarkan, mengirim", jp: "だします" }, { id: "memakai, menggunakan", jp: "つかいます" }, { id: "tiba, sampai", jp: "つきます" }, { id: "membuat, memproduksi", jp: "つくります" }, { id: "pergi mengantar (orang)", jp: "つれていきます" }, { id: "membantu", jp: "てつだいます" }, { id: "menginap, bermalam", jp: "とまります" }, { id: "mengambil, memotret", jp: "とります" }, { id: "memperbaiki", jp: "なおします" }, { id: "hilang, kehilangan", jp: "なくします" }, { id: "belajar", jp: "ならいます" }, { id: "menjadi", jp: "なります" }, { id: "membuka, melepaskan", jp: "ぬぎます" }, { id: "mendaki", jp: "のぼります" }, { id: "minum", jp: "のみます" }, { id: "naik", jp: "のります" }, { id: "masuk", jp: "はいります" }, { id: "memakai (sepatu, celana)", jp: "はきます" }, { id: "bekerja", jp: "はたらきます" }, { id: "menarik", jp: "ひきます" }, { id: "turun (hujan, salju)", jp: "ふります" }, { id: "membayar", jp: "はらいます" }, { id: "berbicara", jp: "はなします" }, { id: "belok (kiri, kanan)", jp: "まがります" }, { id: "menunggu", jp: "まちます" }, { id: "memutar", jp: "まわします" }, { id: "memegang, membawa", jp: "もちます" }, { id: "pergi membawa", jp: "もっていきます" }, { id: "menerima", jp: "もらいます" }, { id: "berguna, bermanfaat", jp: "やくにたちます" }, { id: "beristirahat", jp: "やすみます" }, { id: "memanggil", jp: "よびます" }, { id: "membaca", jp: "よみます" }, { id: "mengerti", jp: "わかります" }, { id: "menyeberang", jp: "わたります" },
            { id: "membuka", jp: "あけます" }, { id: "memberikan", jp: "あげます" }, { id: "mengumpulkan", jp: "あつめます" }, { id: "mandi", jp: "あびます" }, { id: "ada (manusia/hewan)", jp: "います" }, { id: "memasukkan", jp: "いれます" }, { id: "lahir, dilahirkan", jp: "うまれます" }, { id: "bangun (tidur)", jp: "おきます" }, { id: "mengajar, memberitahukan", jp: "おしえます" }, { id: "mengingat, menghafal", jp: "おぼえます" }, { id: "turun (dari kendaraan)", jp: "おります" }, { id: "mengganti, menukar", jp: "かえます" }, { id: "menelepon, memakai", jp: "かけます" }, { id: "memikirkan", jp: "かんがえます" }, { id: "berhati-hati", jp: "きをつけます" }, { id: "memakai (pakaian)", jp: "きます" }, { id: "memberi (kepada saya)", jp: "くれます" }, { id: "menutup", jp: "しめます" }, { id: "memeriksa, menyelidiki", jp: "しらべます" }, { id: "membuang", jp: "すてます" }, { id: "makan", jp: "たべます" }, { id: "cukup, mencukupi", jp: "たります" }, { id: "capek, lelah", jp: "つかれます" }, { id: "menyalakan", jp: "つけます" }, { id: "pergi keluar", jp: "でかけます" }, { id: "dapat, bisa, mampu", jp: "できます" }, { id: "keluar", jp: "でます" }, { id: "menghentikan", jp: "とめます" }, { id: "tidur", jp: "ねます" }, { id: "ganti kendaraan", jp: "のりかえます" }, { id: "memulai", jp: "はじめます" }, { id: "kalah", jp: "まけます" }, { id: "memperlihatkan", jp: "みせます" }, { id: "melihat, menonton", jp: "みます" }, { id: "menjemput", jp: "むかえます" }, { id: "berhenti, meninggalkan", jp: "やめます" }, { id: "lupa", jp: "わすれます" },
            { id: "mengantarkan melihat-lihat", jp: "あんないします" }, { id: "mengemudikan, menjalankan", jp: "うんてんします" }, { id: "berbelanja", jp: "かいものします" }, { id: "datang", jp: "きます" }, { id: "menikah", jp: "けっこんします" }, { id: "meninjau", jp: "けんがくします" }, { id: "meneliti", jp: "けんきゅうします" }, { id: "mengkopi", jp: "コピーします" }, { id: "berjalan-jalan (taman)", jp: "さんぽします" }, { id: "lembur (kerja)", jp: "ざんぎょうします" }, { id: "mengerjakan, berbuat", jp: "します" }, { id: "memperbaiki", jp: "しゅうりします" }, { id: "dinas keluar kota", jp: "しゅっちょうします" }, { id: "memperkenalkan", jp: "しょうかいします" }, { id: "makan (santap)", jp: "しょくじします" }, { id: "khawatir, cemas", jp: "しんぱいします" }, { id: "menjelaskan, menerangkan", jp: "せつめいします" }, { id: "mencuci (pakaian)", jp: "せんたくします" }, { id: "membersihkan (kamar)", jp: "そうじします" }, { id: "membawa, datang mangantar", jp: "つれてきます" }, { id: "menelepon", jp: "でんわします" }, { id: "pindah rumah", jp: "ひっこしします" }, { id: "belajar (bermatapelajaran)", jp: "べんきょうします" }, { id: "membawa, datang membawa", jp: "もってきます" }, { id: "memesan", jp: "よやくします" }, { id: "belajar di luar negeri", jp: "りゅうがくします" }, { id: "berlatih", jp: "れんしゅうします" }
        ];

        const TOTAL_QUESTIONS = 10;
        const TIME_LIMIT = 15; // 15 detik per soal
        let currentQuestion = {};
        let score = 0;
        let questionCount = 1;
        let answered = false;
        let wrongAnswers = [];
        let timerInterval = null;
        let timeLeft = TIME_LIMIT;

        const questionEl = document.getElementById('question');
        const optionsEl = document.getElementById('options');
        const scoreEl = document.getElementById('score');
        const questionNumEl = document.getElementById('question-num');
        const alertModal = document.getElementById('alertModal');
        const finishModal = document.getElementById('finishModal');
        const correctAnswerEl = document.getElementById('correctAnswer');
        const finalScoreEl = document.getElementById('finalScore');
        const retryBtn = document.getElementById('retryBtn');
        const timerEl = document.getElementById('timer');
        const timerBar = document.getElementById('timer-bar');
        const alertTitle = document.getElementById('alertTitle');
        const alertIcon = document.getElementById('alertIcon');

        function getRandomItem(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
        function shuffleArray(arr) { return arr.sort(() => Math.random() - 0.5); }

        function startTimer() {
            clearInterval(timerInterval);
            timeLeft = TIME_LIMIT;
            timerEl.textContent = timeLeft;
            timerBar.classList.remove('warning');
            
            timerInterval = setInterval(() => {
                timeLeft--;
                timerEl.textContent = timeLeft;
                
                if (timeLeft <= 5) {
                    timerBar.classList.add('warning'); // Warna merah berkedip jika sisa 5 detik
                }
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    handleTimeout();
                }
            }, 1000);
        }

        function stopTimer() {
            clearInterval(timerInterval);
        }

        function handleTimeout() {
            if(answered) return;
            answered = true;
            
            // Rekam soal ke daftar salah karena waktu habis
            wrongAnswers.push({
                question: currentQuestion.id,
                correct_answer: currentQuestion.jp,
                user_answer: "Waktu Habis"
            });

            alertIcon.textContent = "⏰";
            alertTitle.textContent = "Waktu Habis!";
            correctAnswerEl.textContent = currentQuestion.jp;
            
            setTimeout(() => alertModal.classList.add('active'), 300);
        }

        function loadQuestion() {
            if (questionCount > TOTAL_QUESTIONS) { finishGame(); return; }
            answered = false;
            currentQuestion = getRandomItem(vocabData);
            questionEl.textContent = currentQuestion.id;
            questionNumEl.textContent = questionCount;

            let options = [currentQuestion];
            while(options.length < 4) {
                let randomWord = getRandomItem(vocabData);
                if(!options.includes(randomWord)) options.push(randomWord);
            }

            options = shuffleArray(options);
            optionsEl.innerHTML = '';
            options.forEach(option => {
                const btn = document.createElement('button');
                btn.className = 'option-btn';
                btn.textContent = option.jp;
                btn.onclick = () => checkAnswer(option, btn);
                optionsEl.appendChild(btn);
            });

            startTimer(); // Mulai timer setiap soal baru dimuat
        }

        function checkAnswer(selectedOption, btn) {
            if(answered) return;
            answered = true;
            stopTimer(); // Hentikan timer saat user klik jawaban

            if(selectedOption.jp === currentQuestion.jp) {
                btn.classList.add('correct');
                score++;
                scoreEl.textContent = score;
                questionCount++;
                setTimeout(loadQuestion, 1000);
            } else {
                btn.classList.add('wrong');
                alertIcon.textContent = "❌";
                alertTitle.textContent = "Jawaban Salah!";
                correctAnswerEl.textContent = currentQuestion.jp;
                
                wrongAnswers.push({
                    question: currentQuestion.id,
                    correct_answer: currentQuestion.jp,
                    user_answer: selectedOption.jp
                });

                setTimeout(() => alertModal.classList.add('active'), 500);
            }
        }

        function finishGame() {
            stopTimer();
            finalScoreEl.textContent = score + " / " + TOTAL_QUESTIONS;
            
            fetch('save_score.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    score: score, 
                    total: TOTAL_QUESTIONS,
                    wrong_answers: wrongAnswers 
                })
            });
            
            finishModal.classList.add('active');
        }

        retryBtn.addEventListener('click', () => {
            alertModal.classList.remove('active');
            questionCount++;
            loadQuestion();
        });

        loadQuestion();
    </script>
</body>
</html>