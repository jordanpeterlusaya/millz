<?php

require_once "../config/database.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    die("Invalid app ID.");
}

/* Get current featured status */
$stmt = $pdo->prepare("
    SELECT featured
    FROM apps
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("App not found.");
}

/* Switch Featured ON/OFF */
$newFeatured = $app["featured"] ? 0 : 1;

$update = $pdo->prepare("
    UPDATE apps
    SET featured = ?
    WHERE id = ?
");

$update->execute([
    $newFeatured,
    $id
]);

/* Return to Apps page */
header("Location: apps.php");
exit;

?>