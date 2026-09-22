<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $game_id = $_POST['game_id'] ?? null;
    $amount  = $_POST['amount'] ?? 0;
    $phone   = trim($_POST['phone'] ?? '');

    if (empty($game_id) || empty($amount) || empty($phone)) {
        echo json_encode(['status' => 'error', 'message' => 'Taarifa za malipo hazijakamilika.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO payments (user_id, game_id, amount, phone, status, created_at) VALUES (:user_id, :game_id, :amount, :phone, 'pending', NOW())");
        $stmt->execute([
            'user_id' => $user_id,
            'game_id' => $game_id,
            'amount'  => $amount,
            'phone'   => $phone
        ]);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Ombi la malipo limeanza. Subiri uthibitisho kwenye simu yako.'
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Imeshindikana kuchakata malipo.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Njia hii haikubaliki.']);
}
?>