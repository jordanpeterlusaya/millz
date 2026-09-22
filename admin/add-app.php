<?php

require_once "../config/database.php";

$message = "";
$error = "";

$categories = $pdo->query("
    SELECT id, name
    FROM categories
    WHERE type = 'app'
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);

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
        $error = "App name is required.";
    } elseif ($download_link !== "") {

        if (!filter_var($download_link, FILTER_VALIDATE_URL)) {
            $error = "Please enter a valid download link.";
        } else {

            $scheme = strtolower(
                parse_url($download_link, PHP_URL_SCHEME) ?? ""
            );

            if (!in_array($scheme, ["http", "https"])) {
                $error = "Download link must use HTTP or HTTPS.";
            }
        }
    }

    /* =========================
       COVER IMAGE
    ========================= */

    $cover_image = null;

    if ($error === "" && isset($_FILES["cover_image"]) &&
        $_FILES["cover_image"]["error"] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES["cover_image"]["error"] !== UPLOAD_ERR_OK) {
            $error = "Cover image upload failed.";
        } else {

            $maxSize = 5 * 1024 * 1024;

            if ($_FILES["cover_image"]["size"] > $maxSize) {
                $error = "Cover image must be 5MB or less.";
            } else {

                $allowed = [
                    "image/jpeg" => "jpg",
                    "image/png"  => "png",
                    "image/webp" => "webp"
                ];

                $mime = mime_content_type(
                    $_FILES["cover_image"]["tmp_name"]
                );

                if (!isset($allowed[$mime])) {
                    $error = "Only JPG, PNG and WEBP images are allowed.";
                } else {

                    $extension = $allowed[$mime];

                    $cover_image =
                        "app_" .
                        time() .
                        "_" .
                        bin2hex(random_bytes(4)) .
                        "." .
                        $extension;

                    $uploadDir = "../uploads/covers/";

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $destination = $uploadDir . $cover_image;

                    if (!move_uploaded_file(
                        $_FILES["cover_image"]["tmp_name"],
                        $destination
                    )) {
                        $error = "Could not save cover image.";
                    }
                }
            }
        }
    }

    /* =========================
       INSERT APP
    ========================= */

    if ($error === "") {

        $priceValue = ($price === "") ? null : $price;

        $stmt = $pdo->prepare("
            INSERT INTO apps
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
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $description,
            $category_id > 0 ? $category_id : null,
            $platform,
            $priceValue,
            $cover_image,
            $download_link !== "" ? $download_link : null,
            $status,
            $featured
        ]);

        $message = "APP ADDED SUCCESSFULLY! 🔥";

        $_POST = [];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add App - MILLZ GAMES</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #080b0f;
    color: #fff;
    font-family: Arial, sans-serif;
}

.container {
    width: 100%;
    max-width: 850px;
    margin: 40px auto;
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.logo {
    color: #00ff88;
    font-size: 28px;
    font-weight: bold;
}

.back {
    text-decoration: none;
    color: #fff;
    background: #151b22;
    padding: 12px 18px;
    border-radius: 8px;
}

.card {
    background: #10151b;
    border: 1px solid #202832;
    border-radius: 15px;
    padding: 25px;
}

label {
    display: block;
    margin: 18px 0 8px;
    font-weight: bold;
}

input,
textarea,
select {
    width: 100%;
    padding: 14px;
    border-radius: 8px;
    border: 1px solid #303944;
    background: #080b0f;
    color: #fff;
    font-size: 15px;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

input:focus,
textarea:focus,
select:focus {
    outline: none;
    border-color: #00ff88;
}

.help {
    color: #8e99a6;
    font-size: 13px;
    margin-top: 6px;
}

.success {
    background: rgba(0,255,136,.12);
    border: 1px solid #00ff88;
    color: #00ff88;
    padding: 14px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.error {
    background: rgba(255,60,60,.12);
    border: 1px solid #ff4444;
    color: #ff7777;
    padding: 14px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
}

.checkbox input {
    width: auto;
}

button {
    width: 100%;
    margin-top: 25px;
    padding: 15px;
    border: none;
    border-radius: 9px;
    background: #00ff88;
    color: #000;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background: #00d875;
}

@media (max-width: 600px) {

    .container {
        margin: 15px auto;
        padding: 12px;
    }

    .header {
        align-items: flex-start;
        flex-direction: column;
    }

    .card {
        padding: 18px;
    }

    .logo {
        font-size: 23px;
    }

}

</style>

</head>

<body>

<div class="container">

    <div class="header">

        <div class="logo">
            MILLZ GAMES
        </div>

        <a href="apps.php" class="back">
            ← Back to Apps
        </a>

    </div>

    <div class="card">

        <h2>Add New App</h2>

        <?php if ($message !== ""): ?>
            <div class="success">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>App Name</label>

            <input
                type="text"
                name="name"
                value="<?= htmlspecialchars($_POST["name"] ?? "") ?>"
                placeholder="Example: Spotify"
                required
            >

            <label>Description</label>

            <textarea
                name="description"
                placeholder="Write app description..."
            ><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

            <label>Category</label>

            <select name="category_id">

                <option value="0">
                    Select Category
                </option>

                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?= (int)$category["id"] ?>"
                        <?= ((int)($_POST["category_id"] ?? 0) === (int)$category["id"]) ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($category["name"]) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <label>Platform</label>

            <select name="platform">

                <option value="">Select Platform</option>

                <option value="Android"
                    <?= (($_POST["platform"] ?? "") === "Android") ? "selected" : "" ?>>
                    Android
                </option>

                <option value="iOS"
                    <?= (($_POST["platform"] ?? "") === "iOS") ? "selected" : "" ?>>
                    iOS
                </option>

                <option value="Windows"
                    <?= (($_POST["platform"] ?? "") === "Windows") ? "selected" : "" ?>>
                    Windows
                </option>

            </select>

            <label>Price</label>

            <input
                type="number"
                step="0.01"
                name="price"
                value="<?= htmlspecialchars($_POST["price"] ?? "") ?>"
                placeholder="Leave empty if not set"
            >

            <label>Download Link</label>

            <input
                type="url"
                name="download_link"
                value="<?= htmlspecialchars($_POST["download_link"] ?? "") ?>"
                placeholder="https://..."
            >

            <div class="help">
                Paste the external download link. Do not upload the app file to XAMPP.
            </div>

            <label>Cover Image</label>

            <input
                type="file"
                name="cover_image"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <div class="help">
                JPG, PNG or WEBP — maximum 5MB.
            </div>

            <label>Status</label>

            <select name="status">

                <option value="published">
                    Published
                </option>

                <option value="unpublished"
                    <?= (($_POST["status"] ?? "") === "unpublished") ? "selected" : "" ?>>
                    Unpublished
                </option>

            </select>

            <div class="checkbox">

                <input
                    type="checkbox"
                    name="featured"
                    id="featured"
                    <?= isset($_POST["featured"]) ? "checked" : "" ?>
                >

                <label for="featured">
                    Featured App
                </label>

            </div>

            <button type="submit">
                ➕ ADD APP
            </button>

        </form>

    </div>

</div>

</body>
</html>