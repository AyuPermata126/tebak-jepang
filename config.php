<?php
session_start();

 $host = 'localhost';
 $dbname = 'db_tebak_jepang';
 $user = 'root';
 $pass = '';

try {
    $pdo_server = new PDO("mysql:host=$host", $user, $pass);
    $pdo_server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_server->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS scores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        score INT NOT NULL,
        total INT NOT NULL,
        played_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Auto Alter: Tambah kolom group_type jika belum ada
    try {
        $pdo->exec("ALTER TABLE scores ADD COLUMN group_type INT DEFAULT 1");
    } catch (PDOException $e) { /* Abaikan jika sudah ada */ }

    $pdo->exec("CREATE TABLE IF NOT EXISTS wrong_answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        score_id INT NOT NULL,
        question_text VARCHAR(255) NOT NULL,
        correct_answer VARCHAR(255) NOT NULL,
        user_answer VARCHAR(255) NOT NULL,
        FOREIGN KEY (score_id) REFERENCES scores(id) ON DELETE CASCADE
    )");

} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function redirect($url) {
    header("Location: $url");
    exit;
}
?>