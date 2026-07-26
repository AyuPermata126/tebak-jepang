<?php
require 'config.php';
if (!isLoggedIn()) exit('Unauthorized');

header('Content-Type: application/json');

 $data = json_decode(file_get_contents('php://input'), true);
 $score = $data['score'] ?? 0;
 $total = $data['total'] ?? 10;
 $group_type = $data['group_type'] ?? 1;
 $wrong_answers = $data['wrong_answers'] ?? [];
 $userId = $_SESSION['user_id'];

try {
    // Simpan nilai utama beserta group_type
    $stmt = $pdo->prepare("INSERT INTO scores (user_id, score, total, group_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $score, $total, $group_type]);
    $scoreId = $pdo->lastInsertId();

    if (!empty($wrong_answers)) {
        $stmtWrong = $pdo->prepare("INSERT INTO wrong_answers (score_id, question_text, correct_answer, user_answer) VALUES (?, ?, ?, ?)");
        foreach ($wrong_answers as $wa) {
            $stmtWrong->execute([
                $scoreId, 
                $wa['question'], 
                $wa['correct_answer'], 
                $wa['user_answer']
            ]);
        }
    }

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>