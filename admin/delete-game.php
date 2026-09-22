<?php

require_once "../config/database.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    die("Invalid game ID.");
}

/* Get game first */
$stmt = $pdo->prepare("
    SELECT cover_image
    FROM games
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    die("Game not found.");
}

try {

    /* Delete game from database */
    $delete = $pdo->prepare("
        DELETE FROM games
        WHERE id = ?
    ");

    $delete->execute([$id]);

    /* Delete cover image */
    if (
        !empty($game["cover_image"]) &&
        file_exists("../uploads/covers/" . $game["cover_image"])
    ) {

        unlink(
            "../uploads/covers/" .
            $game["cover_image"]
        );
    }

    /*
       We DO NOT delete any game file here
       because MILLZ GAMES now uses
       external Download Links.
    */

    header("Location: games.php?deleted=1");
    exit;

} catch (PDOException $e) {

    die("Could not delete game.");

}

?>