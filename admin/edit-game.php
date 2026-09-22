<?php

require_once "../config/database.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    die("Invalid game ID.");
}

/* =========================
   GET GAME
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM games
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    die("Game not found.");
}


/* =========================
   GET CATEGORIES
========================= */

$categoryStmt = $pdo->query("
    SELECT id, name
    FROM categories
    WHERE type = 'game'
    ORDER BY name ASC
");

$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   UPDATE GAME
========================= */

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category_id = !empty($_POST["category_id"])
        ? (int)$_POST["category_id"]
        : null;

    $platform = trim($_POST["platform"] ?? "");

    $priceInput = trim($_POST["price"] ?? "");
    $price = $priceInput !== ""
        ? (float)$priceInput
        : null;

    $download_link = trim($_POST["download_link"] ?? "");

    $status = $_POST["status"] ?? "unpublished";

    $featured = isset($_POST["featured"])
        ? 1
        : 0;


    /* =========================
       BASIC VALIDATION
    ========================= */

    if ($name === "") {

        $error = "Game name is required.";

    } elseif ($download_link !== "") {

        if (!filter_var($download_link, FILTER_VALIDATE_URL)) {

            $error = "Please enter a valid download link.";

        } else {

            $scheme = strtolower(
                parse_url($download_link, PHP_URL_SCHEME) ?? ""
            );

            if (!in_array($scheme, ["http", "https"], true)) {

                $error = "Download link must use HTTP or HTTPS.";

            }

        }

    }


    /* =========================
       COVER IMAGE
    ========================= */

    $newCover = $game["cover_image"];

    if (
        $error === "" &&
        isset($_FILES["cover_image"]) &&
        $_FILES["cover_image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["cover_image"]["error"] !== UPLOAD_ERR_OK) {

            $error = "Cover image upload failed.";

        } else {

            $maxSize = 5 * 1024 * 1024;

            if ($_FILES["cover_image"]["size"] > $maxSize) {

                $error = "Cover image must be 5MB or less.";

            } else {

                $allowedExtensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp"
                ];

                $originalName = $_FILES["cover_image"]["name"];

                $extension = strtolower(
                    pathinfo($originalName, PATHINFO_EXTENSION)
                );

                if (!in_array($extension, $allowedExtensions, true)) {

                    $error = "Invalid cover image format.";

                } else {

                    $uploadDirectory = "../uploads/covers/";

                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0777, true);
                    }

                    $newFileName =
                        "game_" .
                        time() .
                        "_" .
                        bin2hex(random_bytes(4)) .
                        "." .
                        $extension;

                    $destination =
                        $uploadDirectory .
                        $newFileName;

                    if (
                        !move_uploaded_file(
                            $_FILES["cover_image"]["tmp_name"],
                            $destination
                        )
                    ) {

                        $error = "Could not save cover image.";

                    } else {

                        $newCover = $newFileName;

                    }
                }
            }
        }
    }


    /* =========================
       SAVE DATABASE
    ========================= */

    if ($error === "") {

        try {

            $update = $pdo->prepare("
                UPDATE games
                SET
                    name = ?,
                    description = ?,
                    category_id = ?,
                    platform = ?,
                    price = ?,
                    cover_image = ?,
                    download_link = ?,
                    status = ?,
                    featured = ?
                WHERE id = ?
            ");

            $update->execute([
                $name,
                $description,
                $category_id,
                $platform,
                $price,
                $newCover,
                $download_link !== "" ? $download_link : null,
                $status,
                $featured,
                $id
            ]);


            /* Delete old cover after successful update */

            if (
                !empty($game["cover_image"]) &&
                $newCover !== $game["cover_image"]
            ) {

                $oldCover =
                    "../uploads/covers/" .
                    $game["cover_image"];

                if (file_exists($oldCover)) {
                    unlink($oldCover);
                }
            }


            header("Location: games.php?updated=1");
            exit;

        } catch (PDOException $e) {

            /* Remove newly uploaded cover if DB update failed */

            if (
                $newCover !== $game["cover_image"] &&
                !empty($newCover)
            ) {

                $newCoverPath =
                    "../uploads/covers/" .
                    $newCover;

                if (file_exists($newCoverPath)) {
                    unlink($newCoverPath);
                }
            }

            $error = "Could not update game.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Edit Game - MILLZ GAMES</title>

<link
    href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Rajdhani:wght@500;600;700&display=swap"
    rel="stylesheet"
>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    background:
        radial-gradient(
            circle at top,
            #14231d 0%,
            #050807 45%,
            #020303 100%
        );
    color: #ffffff;
    font-family: "Rajdhani", sans-serif;
}

.container {
    width: 100%;
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.topbar h1 {
    margin: 0;
    font-family: "Orbitron", sans-serif;
    color: #00ff88;
    font-size: 25px;
}

.back-btn {
    text-decoration: none;
    color: #ffffff;
    border: 1px solid #00ff88;
    padding: 10px 16px;
    border-radius: 8px;
    transition: 0.2s;
}

.back-btn:hover {
    background: #00ff88;
    color: #000000;
}

.card {
    background: rgba(10, 15, 13, 0.95);
    border: 1px solid #1d3028;
    border-radius: 14px;
    padding: 25px;
}

.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-weight: 700;
    color: #d9e7df;
}

input,
textarea,
select {
    width: 100%;
    padding: 13px;
    border-radius: 8px;
    border: 1px solid #30463c;
    background: #07100c;
    color: #ffffff;
    font-family: inherit;
    font-size: 16px;
    outline: none;
}

input:focus,
textarea:focus,
select:focus {
    border-color: #00ff88;
}

textarea {
    min-height: 130px;
    resize: vertical;
}

.current-cover {
    margin-top: 10px;
}

.current-cover img {
    width: 150px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #30463c;
}

.checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 18px 0;
}

.checkbox-row input {
    width: 20px;
    height: 20px;
}

.checkbox-row label {
    margin: 0;
}

.submit-btn {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 9px;
    background: #00ff88;
    color: #000000;
    font-family: "Orbitron", sans-serif;
    font-weight: 700;
    cursor: pointer;
    font-size: 15px;
}

.submit-btn:hover {
    opacity: 0.9;
}

.error {
    background: #3a1111;
    border: 1px solid #ff4d4d;
    color: #ffb5b5;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info {
    background: #10251c;
    border: 1px solid #00ff88;
    color: #caffdf;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.small {
    color: #8fa79b;
    font-size: 14px;
    margin-top: 5px;
}

@media (max-width: 600px) {

    .container {
        margin: 20px auto;
        padding: 14px;
    }

    .topbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .topbar h1 {
        font-size: 20px;
    }

    .card {
        padding: 18px;
    }

}

</style>

</head>

<body>

<div class="container">

    <div class="topbar">

        <h1>EDIT GAME</h1>

        <a
            href="games.php"
            class="back-btn"
        >
            ← Back to Games
        </a>

    </div>


    <div class="card">

        <?php if ($error !== ""): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <?php if ($success !== ""): ?>

            <div class="info">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="form-group">

                <label>Game Name</label>

                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($game["name"] ?? "") ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                ><?= htmlspecialchars($game["description"] ?? "") ?></textarea>

            </div>


            <div class="form-group">

                <label>Category</label>

                <select name="category_id">

                    <option value="">
                        Select Category
                    </option>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?= (int)$category["id"] ?>"
                            <?= ((int)($game["category_id"] ?? 0) === (int)$category["id"]) ? "selected" : "" ?>
                        >
                            <?= htmlspecialchars($category["name"]) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label>Platform</label>

                <input
                    type="text"
                    name="platform"
                    value="<?= htmlspecialchars($game["platform"] ?? "") ?>"
                    placeholder="Android / Windows / iOS"
                >

            </div>


            <div class="form-group">

                <label>Price</label>

                <input
                    type="number"
                    name="price"
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars($game["price"] ?? "") ?>"
                    placeholder="Leave empty if price is not set"
                >

                <div class="small">
                    You can set the price later.
                </div>

            </div>


            <div class="form-group">

                <label>Download Link</label>

                <input
                    type="url"
                    name="download_link"
                    value="<?= htmlspecialchars($game["download_link"] ?? "") ?>"
                    placeholder="https://..."
                >

                <div class="small">
                    Customer will be sent to this link after access-code verification.
                </div>

            </div>


            <div class="form-group">

                <label>Status</label>

                <select name="status">

                    <option
                        value="published"
                        <?= (($game["status"] ?? "") === "published") ? "selected" : "" ?>
                    >
                        Published
                    </option>

                    <option
                        value="unpublished"
                        <?= (($game["status"] ?? "") === "unpublished") ? "selected" : "" ?>
                    >
                        Unpublished
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>Replace Cover Image</label>

                <input
                    type="file"
                    name="cover_image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <div class="small">
                    Maximum 5MB.
                </div>


                <?php if (!empty($game["cover_image"])): ?>

                    <div class="current-cover">

                        <p>Current Cover:</p>

                        <img
                            src="../uploads/covers/<?= htmlspecialchars($game["cover_image"]) ?>"
                            alt="Current game cover"
                        >

                    </div>

                <?php endif; ?>

            </div>


            <div class="checkbox-row">

                <input
                    type="checkbox"
                    id="featured"
                    name="featured"
                    value="1"
                    <?= !empty($game["featured"]) ? "checked" : "" ?>
                >

                <label for="featured">
                    Featured Game
                </label>

            </div>


            <button
                type="submit"
                class="submit-btn"
            >
                UPDATE GAME
            </button>

        </form>

    </div>

</div>

</body>

</html>