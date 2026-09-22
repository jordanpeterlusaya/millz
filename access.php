<?php

require_once "config/database.php";

$accessGranted = false;
$errorMessage = "";

$code = isset($_POST["code"])
    ? strtoupper(trim($_POST["code"]))
    : "";

$productType = "";
$productId = 0;
$productName = "";
$coverImage = "";
$expiresAt = "";
$remainingSeconds = 0;
$downloadLink = "";


/* =========================
   PROCESS ACCESS CODE
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($code === "") {

        $errorMessage =
            "HEY, DON'T TRY ME! WRONG PASSWORD. 💀";

    } else {

        try {

            /* =========================
               FIND CODE
            ========================= */

            $stmt = $pdo->prepare("
                SELECT
                    ac.*,

                    g.name AS game_name,
                    g.cover_image AS game_cover,
                    g.status AS game_status,
                    g.download_link AS game_download_link,

                    a.name AS app_name,
                    a.cover_image AS app_cover,
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

                $errorMessage =
                    "HEY, DON'T TRY ME! WRONG PASSWORD. 💀";

            }

            /* =========================
               DISABLED
            ========================= */

            elseif ($access["status"] === "disabled") {

                $errorMessage =
                    "This access code has been disabled.";

            }

            /* =========================
               CHECK EXPIRY
            ========================= */

            elseif (
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

                $errorMessage =
                    "YOUR CODE IS DEAD! GET A NEW ONE AND TRY AGAIN. 💀";

            }

            else {

                /* =========================
                   ACTIVATE NEW CODE
                   24 HOURS START NOW
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


                    /* GET UPDATED TIMES */

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

                    $times =
                        $stmt->fetch(PDO::FETCH_ASSOC);

                    $access["activated_at"] =
                        $times["activated_at"];

                    $access["expires_at"] =
                        $times["expires_at"];
                }


                /* =========================
                   GAME
                ========================= */

                if (!empty($access["game_id"])) {

                    $productType = "game";

                    $productId =
                        (int)$access["game_id"];

                    $productName =
                        $access["game_name"];

                    $coverImage =
                        $access["game_cover"] ?? "";

                    $productStatus =
                        $access["game_status"];

                    $downloadLink =
                        trim(
                            $access["game_download_link"]
                            ?? ""
                        );

                }

                /* =========================
                   APP
                ========================= */

                elseif (!empty($access["app_id"])) {

                    $productType = "app";

                    $productId =
                        (int)$access["app_id"];

                    $productName =
                        $access["app_name"];

                    $coverImage =
                        $access["app_cover"] ?? "";

                    $productStatus =
                        $access["app_status"];

                    $downloadLink =
                        trim(
                            $access["app_download_link"]
                            ?? ""
                        );

                }

                else {

                    $errorMessage =
                        "This code is not connected to a game or app.";
                }


                /* =========================
                   PRODUCT CHECK
                ========================= */

                if (
                    $errorMessage === "" &&
                    $productStatus !== "published"
                ) {

                    $errorMessage =
                        "This product is currently unavailable.";
                }


                /* =========================
                   DOWNLOAD LINK CHECK
                ========================= */

                if (
                    $errorMessage === "" &&
                    $downloadLink === ""
                ) {

                    $errorMessage =
                        "Download link is not available yet.";
                }


                /* =========================
                   VALIDATE LINK
                ========================= */

                if (
                    $errorMessage === "" &&
                    !filter_var(
                        $downloadLink,
                        FILTER_VALIDATE_URL
                    )
                ) {

                    $errorMessage =
                        "Invalid download link.";
                }


                if (
                    $errorMessage === "" &&
                    !in_array(
                        strtolower(
                            parse_url(
                                $downloadLink,
                                PHP_URL_SCHEME
                            ) ?? ""
                        ),
                        ["http", "https"]
                    )
                ) {

                    $errorMessage =
                        "Invalid download link.";
                }


                /* =========================
                   SUCCESS
                ========================= */

                if ($errorMessage === "") {

                    $accessGranted = true;

                    $expiresAt =
                        $access["expires_at"];

                    $remainingSeconds =
                        max(
                            0,
                            strtotime($expiresAt) - time()
                        );
                }
            }

        } catch (PDOException $e) {

            $errorMessage =
                "Server error. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php
$pageTitle = "Access | MILLZ GAMES";
$current = "access";
require "includes/site-head.php";
?>
<style>
.access-page { min-height: 70vh; display:flex; align-items:center; justify-content:center; padding:40px 0 70px; }
.access-card { width:min(520px,92%); background:#0d1316; border:1px solid rgba(0,255,136,.25); border-radius:18px; padding:32px 28px; box-shadow:0 20px 60px rgba(0,0,0,.55); }
.access-form { display:flex; flex-direction:column; gap:13px; }
.access-form input { width:100%; padding:15px; border-radius:10px; border:1px solid #273238; background:#11181c; color:#fff; outline:none; font-size:17px; text-align:center; letter-spacing:2px; }
.access-form input:focus { border-color:#00ff88; }
.access-form button { border:0; padding:14px; border-radius:10px; background:#00ff88; color:#03130b; font-size:17px; font-weight:900; cursor:pointer; font-family:inherit; }
.access-success { text-align:center; }
.success-badge { display:inline-block; padding:7px 13px; border-radius:20px; background:#00ff88; color:#03130b; font-weight:900; margin-bottom:18px; }
.access-cover { width:180px; height:180px; margin:0 auto 18px; border-radius:15px; overflow:hidden; background:#151b1f; }
.access-cover img { width:100%; height:100%; object-fit:cover; }
.timer-box { background:#11181c; border:1px solid #263238; border-radius:12px; padding:16px; margin:18px 0; }
.timer { color:#00ff88; font-family:Syne,sans-serif; font-size:25px; font-weight:800; }
.download-btn { display:block; width:100%; padding:14px; border-radius:10px; background:#00ff88; color:#03130b; font-size:17px; font-weight:900; margin-bottom:12px; text-align:center; }
</style>
</head>
<body>
<?php require "includes/site-header.php"; ?>

<main class="access-page">
<div class="access-card">
    <div class="section-kicker" style="text-align:center;">Access</div>
    <h1 style="font-family:Syne,sans-serif;font-size:42px;text-align:center;line-height:1.05;letter-spacing:-.03em;margin:8px 0 12px;">
        Access <span style="color:#00ff88;">code</span>
    </h1>

    <?php if (!$accessGranted): ?>
        <p style="text-align:center;color:#8f9c97;margin-bottom:22px;">Enter your code to unlock the download.</p>

        <?php if ($errorMessage !== ""): ?>
            <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <form method="POST" class="access-form">
            <input type="text" name="code" placeholder="MILLZXXXXXX" value="<?= htmlspecialchars($code) ?>" autocomplete="off" required>
            <button type="submit"><i class="fa-solid fa-unlock"></i> Unlock</button>
        </form>
    <?php else: ?>
        <div class="access-success">
            <div class="success-badge">ACCESS GRANTED</div>

            <?php if ($coverImage !== ""): ?>
                <div class="access-cover">
                    <img src="uploads/covers/<?= htmlspecialchars($coverImage) ?>" alt="<?= htmlspecialchars($productName) ?>">
                </div>
            <?php endif; ?>

            <h2 class="product-name" style="font-family:Syne,sans-serif;margin-bottom:6px;"><?= htmlspecialchars($productName) ?></h2>
            <div style="color:#00ff88;font-weight:800;margin-bottom:14px;"><?= strtoupper(htmlspecialchars($productType)) ?></div>

            <div class="timer-box">
                <div style="color:#8f9c97;font-size:14px;margin-bottom:5px;">ACCESS EXPIRES IN</div>
                <div class="timer" id="countdown" data-seconds="<?= (int)$remainingSeconds ?>">Loading...</div>
            </div>

            <a href="download.php?code=<?= urlencode($code) ?>&type=<?= urlencode($productType) ?>&id=<?= (int)$productId ?>" class="download-btn">
                DOWNLOAD NOW
            </a>
            <a href="index.php" class="btn btn-outline" style="width:100%;">Back to MILLZ GAMES</a>
        </div>
    <?php endif; ?>
</div>
</main>

<?php require "includes/site-footer.php"; ?>

<?php if ($accessGranted): ?>
<script>
(function () {
    const timer = document.getElementById("countdown");
    if (!timer) return;
    let seconds = parseInt(timer.dataset.seconds, 10);
    if (Number.isNaN(seconds) || seconds < 0) seconds = 0;

    function updateTimer() {
        const days = Math.floor(seconds / 86400);
        const hours = Math.floor((seconds % 86400) / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        timer.textContent =
            String(days).padStart(2, "0") + "D " +
            String(hours).padStart(2, "0") + "H " +
            String(minutes).padStart(2, "0") + "M " +
            String(secs).padStart(2, "0") + "S";
        if (seconds <= 0) {
            timer.textContent = "EXPIRED";
            return;
        }
        seconds--;
        setTimeout(updateTimer, 1000);
    }
    updateTimer();
})();
</script>
<?php endif; ?>
</body>
</html>
