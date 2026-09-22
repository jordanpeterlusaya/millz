<?php

require_once "../config/database.php";

header("Content-Type: application/json; charset=UTF-8");


/* =========================
   REQUEST METHOD
========================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


/* =========================
   GET CODE
========================= */

$code = isset($_POST["code"])
    ? strtoupper(trim($_POST["code"]))
    : "";

if ($code === "") {

    echo json_encode([
        "success" => false,
        "message" => "Please enter your access code."
    ]);

    exit;
}


try {

    /* =========================
       FIND ACCESS CODE
    ========================= */

    $stmt = $pdo->prepare("
        SELECT
            ac.*,

            g.name AS game_name,
            g.status AS game_status,
            g.download_link AS game_download_link,

            a.name AS app_name,
            a.status AS app_status,
            a.download_link AS app_download_link

        FROM access_codes ac

        LEFT JOIN games g
            ON ac.game_id = g.id

        LEFT JOIN apps a
            ON ac.app_id = a.id

        WHERE ac.code = ?

        LIMIT 1
    ");

    $stmt->execute([$code]);

    $access = $stmt->fetch(PDO::FETCH_ASSOC);


    /* =========================
       CODE NOT FOUND
    ========================= */

    if (!$access) {

        echo json_encode([
            "success" => false,
            "message" => "HEY, DON'T TRY ME! WRONG PASSWORD. 💀"
        ]);

        exit;
    }


    /* =========================
       DISABLED CODE
    ========================= */

    if ($access["status"] === "disabled") {

        echo json_encode([
            "success" => false,
            "message" => "This access code has been disabled."
        ]);

        exit;
    }


    /* =========================
       EXPIRED CODE
    ========================= */

    if (
        !empty($access["expires_at"]) &&
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


        echo json_encode([
            "success" => false,
            "message" => "YOUR CODE IS DEAD! GET A NEW ONE AND TRY AGAIN. 💀"
        ]);

        exit;
    }


    /* =========================
       ACTIVATE CODE
       24 HOURS
    ========================= */

    if (
        empty($access["activated_at"]) &&
        empty($access["expires_at"])
    ) {

        $update = $pdo->prepare("
            UPDATE access_codes

            SET
                activated_at = NOW(),
                expires_at = DATE_ADD(NOW(), INTERVAL 24 HOUR),
                status = 'active'

            WHERE id = ?
        ");

        $update->execute([
            $access["id"]
        ]);


        /* GET NEW EXPIRY */

        $stmt = $pdo->prepare("
            SELECT
                activated_at,
                expires_at

            FROM access_codes

            WHERE id = ?

            LIMIT 1
        ");

        $stmt->execute([
            $access["id"]
        ]);

        $times = $stmt->fetch(PDO::FETCH_ASSOC);


        $access["activated_at"] =
            $times["activated_at"];

        $access["expires_at"] =
            $times["expires_at"];
    }


    /* =========================
       DETERMINE PRODUCT
    ========================= */

    if (!empty($access["game_id"])) {

        $productType = "game";

        $productId = (int)$access["game_id"];

        $productName =
            $access["game_name"];

        $productStatus =
            $access["game_status"];

        $downloadLink =
            trim(
                $access["game_download_link"] ?? ""
            );

    } elseif (!empty($access["app_id"])) {

        $productType = "app";

        $productId = (int)$access["app_id"];

        $productName =
            $access["app_name"];

        $productStatus =
            $access["app_status"];

        $downloadLink =
            trim(
                $access["app_download_link"] ?? ""
            );

    } else {

        echo json_encode([
            "success" => false,
            "message" => "This code is not connected to a game or app."
        ]);

        exit;
    }


    /* =========================
       PRODUCT STATUS
    ========================= */

    if ($productStatus !== "published") {

        echo json_encode([
            "success" => false,
            "message" => "This product is currently unavailable."
        ]);

        exit;
    }


    /* =========================
       DOWNLOAD LINK
    ========================= */

    if ($downloadLink === "") {

        echo json_encode([
            "success" => false,
            "message" => "Download link is not available yet."
        ]);

        exit;
    }


    /* =========================
       VALIDATE DOWNLOAD LINK
    ========================= */

    if (!filter_var($downloadLink, FILTER_VALIDATE_URL)) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid download link."
        ]);

        exit;
    }


    $scheme = strtolower(
        parse_url(
            $downloadLink,
            PHP_URL_SCHEME
        ) ?? ""
    );


    if (!in_array($scheme, ["http", "https"])) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid download link."
        ]);

        exit;
    }


    /* =========================
       REMAINING TIME
    ========================= */

    $expiresTimestamp =
        strtotime($access["expires_at"]);

    $remainingSeconds =
        max(
            0,
            $expiresTimestamp - time()
        );


    /* =========================
       SUCCESS
    ========================= */

    echo json_encode([

        "success" => true,

        "message" => "ACCESS GRANTED! 🔓",

        "code" =>
            $access["code"],

        "product_type" =>
            $productType,

        "product_id" =>
            $productId,

        "product_name" =>
            $productName,

        "download_link" =>
            $downloadLink,

        "activated_at" =>
            $access["activated_at"],

        "expires_at" =>
            $access["expires_at"],

        "remaining_seconds" =>
            $remainingSeconds

    ]);

    exit;


} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Server error. Please try again."
    ]);

    exit;
}

?>