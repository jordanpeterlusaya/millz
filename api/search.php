<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    echo json_encode([]);
    exit();
}

try {
    // Tafuta mchezo kwa kutumia jina au category
    $stmt = $pdo->prepare("SELECT id, title, image, price, platform FROM games WHERE title LIKE :query OR category LIKE :query LIMIT 10");
    $stmt->execute(['query' => '%' . $query . '%']);
    $games = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $games
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Query error: ' . $e->getMessage()
    ]);
}
?>