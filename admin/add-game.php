<?php

require_once "../config/database.php";

$message = "";
$messageType = "";

$categoriesStmt = $pdo->query("
    SELECT id, name
    FROM categories
    WHERE type = 'game'
    ORDER BY name ASC
");

$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category_id = (int)($_POST["category_id"] ?? 0);
    $platform = trim($_POST["platform"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $status = $_POST["status"] ?? "unpublished";
    $featured = isset($_POST["featured"]) ? 1 : 0;
    $download_link = trim($_POST["download_link"] ?? "");

    if ($name === "") {
        $message = "Please enter the game name.";
        $messageType = "error";

    } elseif ($category_id <= 0) {
        $message = "Please select a category.";
        $messageType = "error";

    } elseif ($platform === "") {
        $message = "Please enter the platform.";
        $messageType = "error";

    } elseif ($download_link === "") {
        $message = "Please enter the download link.";
        $messageType = "error";

    } elseif (!filter_var($download_link, FILTER_VALIDATE_URL)) {
        $message = "Please enter a valid download link.";
        $messageType = "error";

    } elseif (
        !preg_match(
            '/^https?:\/\//i',
            $download_link
        )
    ) {
        $message = "Download link must start with http:// or https://";
        $messageType = "error";

    } else {

        $coverImage = null;

        /*
        ==========================
        COVER IMAGE UPLOAD
        ==========================
        */

        if (
            isset($_FILES["cover_image"]) &&
            $_FILES["cover_image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES["cover_image"]["error"] !== UPLOAD_ERR_OK) {

                $message = "Cover image upload failed.";
                $messageType = "error";

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

                $fileSize = $_FILES["cover_image"]["size"];

                if (!in_array($extension, $allowedExtensions, true)) {

                    $message = "Invalid cover image format.";
                    $messageType = "error";

                } elseif ($fileSize > 5 * 1024 * 1024) {

                    $message = "Cover image must be 5MB or less.";
                    $messageType = "error";

                } else {

                    $coverImage =
                        "game_" .
                        time() .
                        "_" .
                        bin2hex(random_bytes(4)) .
                        "." .
                        $extension;

                    $uploadDirectory = "../uploads/covers/";

                    if (!is_dir($uploadDirectory)) {
                        mkdir(
                            $uploadDirectory,
                            0755,
                            true
                        );
                    }

                    $uploadPath =
                        $uploadDirectory .
                        $coverImage;

                    if (
                        !move_uploaded_file(
                            $_FILES["cover_image"]["tmp_name"],
                            $uploadPath
                        )
                    ) {

                        $message = "Could not save cover image.";
                        $messageType = "error";
                        $coverImage = null;
                    }
                }
            }
        }

        /*
        ==========================
        SAVE GAME
        ==========================
        */

        if ($message === "") {

            try {

                $priceValue = null;

                if ($price !== "") {
                    $priceValue = (float)$price;
                }

                $stmt = $pdo->prepare("
                    INSERT INTO games
                    (
                        name,
                        description,
                        category_id,
                        platform,
                        price,
                        cover_image,
                        download_link,
                        status,
                        featured
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )
                ");

                $stmt->execute([
                    $name,
                    $description,
                    $category_id,
                    $platform,
                    $priceValue,
                    $coverImage,
                    $download_link,
                    $status,
                    $featured
                ]);

                $message = "GAME ADDED SUCCESSFULLY! 🔥";
                $messageType = "success";

                /*
                Clear form values
                */
                $name = "";
                $description = "";
                $category_id = 0;
                $platform = "";
                $price = "";
                $download_link = "";
                $status = "unpublished";
                $featured = 0;

            } catch (PDOException $e) {

                /*
                Delete uploaded cover if database insert fails
                */
                if (
                    !empty($coverImage) &&
                    file_exists("../uploads/covers/" . $coverImage)
                ) {
                    unlink(
                        "../uploads/covers/" .
                        $coverImage
                    );
                }

                $message = "Could not add game. Please try again.";
                $messageType = "error";
            }
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

<title>Add Game - MILLZ GAMES</title>

<link
    href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700;800&family=Rajdhani:wght@500;600;700&display=swap"
    rel="stylesheet"
>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background:
        radial-gradient(
            circle at top right,
            rgba(0,255,136,0.08),
            transparent 35%
        ),
        #070b0a;
    color: #ffffff;
    font-family: "Rajdhani", sans-serif;
}

.container {
    width: 100%;
    max-width: 950px;
    margin: auto;
    padding: 30px 20px 60px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.logo {
    font-family: "Orbitron", sans-serif;
    color: #00ff88;
    font-size: 25px;
    font-weight: 800;
}

.back {
    text-decoration: none;
    color: #ffffff;
    border: 1px solid #24352e;
    padding: 10px 16px;
    border-radius: 10px;
    background: #101714;
}

.back:hover {
    border-color: #00ff88;
    color: #00ff88;
}

.card {
    background: #0d1311;
    border: 1px solid #1d2b25;
    border-radius: 18px;
    padding: 25px;
}

h1 {
    margin-top: 0;
    font-family: "Orbitron", sans-serif;
    font-size: 25px;
}

.subtitle {
    color: #8ea39a;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 700;
}

input,
textarea,
select {
    width: 100%;
    padding: 13px 14px;
    border-radius: 10px;
    border: 1px solid #263a31;
    background: #080d0b;
    color: #ffffff;
    outline: none;
    font-family: inherit;
    font-size: 16px;
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

.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.link-info {
    margin-top: 7px;
    color: #7f958b;
    font-size: 14px;
}

.check-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 15px 0;
}

.check-row input {
    width: auto;
}

button {
    width: 100%;
    border: none;
    padding: 15px;
    border-radius: 11px;
    background: #00ff88;
    color: #041009;
    font-family: "Orbitron", sans-serif;
    font-weight: 800;
    cursor: pointer;
    font-size: 15px;
}

button:hover {
    background: #00d875;
}

.message {
    padding: 13px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: 700;
}

.success {
    background: rgba(0,255,136,0.10);
    border: 1px solid #00ff88;
    color: #00ff88;
}

.error {
    background: rgba(255,60,60,0.10);
    border: 1px solid #ff4d4d;
    color: #ff7070;
}

@media (max-width: 650px) {

    .container {
        padding: 20px 14px 40px;
    }

    .card {
        padding: 18px;
    }

    .row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .logo {
        font-size: 21px;
    }

    h1 {
        font-size: 21px;
    }
}

</style>

</head>

<body>

<div class="container">

    <div class="topbar">

        <div class="logo">
            MILLZ GAMES
        </div>

        <a
            href="games.php"
            class="back"
        >
            ← Back to Games
        </a>

    </div>

    <div class="card">

        <h1>
            ADD NEW GAME 🎮
        </h1>

        <div class="subtitle">
            Add the game information and external download link.
        </div>

        <?php if ($message !== ""): ?>

            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="form-group">

                <label>
                    Game Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($name ?? '') ?>"
                    placeholder="Example: Subway Surfers"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    placeholder="Write game description..."
                ><?= htmlspecialchars($description ?? '') ?></textarea>

            </div>

            <div class="row">

                <div class="form-group">

                    <label>
                        Category
                    </label>

                    <select
                        name="category_id"
                        required
                    >

                        <option value="">
                            Select Category
                        </option>

                        <?php foreach ($categories as $category): ?>

                            <option
                                value="<?= (int)$category["id"] ?>"
                                <?= (
                                    isset($category_id) &&
                                    (int)$category_id === (int)$category["id"]
                                )
                                    ? "selected"
                                    : ""
                                ?>
                            >
                                <?= htmlspecialchars($category["name"]) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>
                        Platform
                    </label>

                    <input
                        type="text"
                        name="platform"
                        value="<?= htmlspecialchars($platform ?? '') ?>"
                        placeholder="Example: Android"
                        required
                    >

                </div>

            </div>

            <div class="row">

                <div class="form-group">

                    <label>
                        Price
                    </label>

                    <input
                        type="number"
                        name="price"
                        value="<?= htmlspecialchars($price ?? '') ?>"
                        placeholder="Example: 5000"
                        min="0"
                        step="0.01"
                    >

                    <div class="link-info">
                        Leave empty if price is not set yet.
                    </div>

                </div>

                <div class="form-group">

                    <label>
                        Publishing Status
                    </label>

                    <select name="status">

                        <option
                            value="unpublished"
                            <?= (($status ?? '') === "unpublished")
                                ? "selected"
                                : ""
                            ?>
                        >
                            Unpublished
                        </option>

                        <option
                            value="published"
                            <?= (($status ?? '') === "published")
                                ? "selected"
                                : ""
                            ?>
                        >
                            Published
                        </option>

                    </select>

                </div>

            </div>

            <div class="form-group">

                <label>
                    Download Link 🔗
                </label>

                <input
                    type="url"
                    name="download_link"
                    value="<?= htmlspecialchars($download_link ?? '') ?>"
                    placeholder="https://example.com/download"
                    required
                >

                <div class="link-info">
                    Put the external download link here. The game file will NOT be uploaded to MILLZ GAMES.
                </div>

            </div>

            <div class="form-group">

                <label>
                    Cover Image
                </label>

                <input
                    type="file"
                    name="cover_image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <div class="link-info">
                    JPG, JPEG, PNG or WEBP — maximum 5MB.
                </div>

            </div>

            <div class="check-row">

                <input
                    type="checkbox"
                    name="featured"
                    id="featured"
                    <?= !empty($featured) ? "checked" : "" ?>
                >

                <label
                    for="featured"
                    style="margin:0;"
                >
                    Featured Game
                </label>

            </div>

            <button type="submit">
                ADD GAME 🚀
            </button>

        </form>

    </div>

</div>

</body>

</html>