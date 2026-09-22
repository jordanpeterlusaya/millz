<?php

require_once "../config/database.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    die("Invalid app ID.");
}


/* =========================
   FIND APP
========================= */

$stmt = $pdo->prepare("
    SELECT cover_image
    FROM apps
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("App not found.");
}


/* =========================
   DELETE APP
========================= */

try {

    $delete = $pdo->prepare("
        DELETE FROM apps
        WHERE id = ?
    ");

    $delete->execute([$id]);


    /* =========================
       DELETE COVER IMAGE
    ========================= */

    if (
        !empty($app["cover_image"]) &&
        file_exists("../uploads/covers/" . $app["cover_image"])
    ) {

        unlink(
            "../uploads/covers/" . $app["cover_image"]
        );
    }


    header("Location: apps.php?deleted=1");
    exit;

} catch (PDOException $e) {

    die("Could not delete app.");

}

?>