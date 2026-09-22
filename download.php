<?php

require_once "config/database.php";

$code = isset($_GET["code"])
    ? strtoupper(trim($_GET["code"]))
    : "";

$type = isset($_GET["type"])
    ? strtolower(trim($_GET["type"]))
    : "";

$id = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;


/* =========================
   BASIC VALIDATION
========================= */

if ($code === "" || $id <= 0 || !in_array($type, ["game", "app"])) {
    die("Invalid download request.");
}


/* =========================
   FIND ACCESS CODE
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM access_codes
    WHERE code = ?
    LIMIT 1
");

$stmt->execute([$code]);

$access = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$access) {
    die("Invalid access code.");
}


/* =========================
   CHECK STATUS
========================= */

if ($access["status"] === "disabled") {
    die("This access code has been disabled.");
}


/* =========================
   CHECK EXPIRY
========================= */

if (
    empty($access["expires_at"]) ||
    strtotime($access["expires_at"]) <= time()
) {

    $update = $pdo->prepare("
        UPDATE access_codes
        SET status = 'expired'
        WHERE id = ?
    ");

    $update->execute([
        $access["id"]
    ]);

    die("YOUR CODE IS DEAD! GET A NEW ONE AND TRY AGAIN. 💀");
}


/* =========================
   CHECK PRODUCT
========================= */

if ($type === "game") {

    if ((int)$access["game_id"] !== $id) {
        die("This access code is not valid for this game.");
    }

    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            status,
            download_link
        FROM games
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

} else {

    if ((int)$access["app_id"] !== $id) {
        die("This access code is not valid for this app.");
    }

    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            status,
            download_link
        FROM apps
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}


/* =========================
   PRODUCT CHECK
========================= */

if (!$product) {
    die("Product not found.");
}

if ($product["status"] !== "published") {
    die("This product is currently unavailable.");
}


/* =========================
   DOWNLOAD LINK CHECK
========================= */

$downloadLink = trim($product["download_link"] ?? "");

if ($downloadLink === "") {
    die("Download link is not available yet.");
}


/* =========================
   SECURITY CHECK
========================= */

if (!filter_var($downloadLink, FILTER_VALIDATE_URL)) {
    die("Invalid download link.");
}

$urlScheme = strtolower(
    parse_url($downloadLink, PHP_URL_SCHEME) ?? ""
);

if (!in_array($urlScheme, ["http", "https"])) {
    die("Invalid download link.");
}


/* =========================
   REDIRECT
========================= */

header(
    "Location: " . $downloadLink,
    true,
    302
);

exit;

?>