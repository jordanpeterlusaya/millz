<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Tafadhali jaza nafasi zote.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())");
        $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);

        echo json_encode(['status' => 'success', 'message' => 'Ujumbe wako umetumwa kikamilifu!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Ujumbe haujatumwa, jaribu tena.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Njia hii haikubaliki.']);
}
?>