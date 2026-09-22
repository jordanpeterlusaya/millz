<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_name = trim($_POST['game_name'] ?? '');
    $user_email = trim($_POST['email'] ?? '');

    if (empty($game_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Tafadhali andika jina la game.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO game_requests (game_name, email, created_at) VALUES (:game_name, :email, NOW())");
        $stmt->execute(['game_name' => $game_name, 'email' => $user_email]);

        echo json_encode(['status' => 'success', 'message' => 'Ombi lako limepokelewa kikamilifu!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Imeshindikana kuhifadhi ombi.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Njia hii haikubaliki.']);
}
?>