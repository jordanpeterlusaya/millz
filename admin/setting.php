<?php
require_once "../config/database.php";

$message = "";
$error = "";

// Settings zinazotumika na website
$defaults = [
    "site_name" => "MILLZ GAMES",
    "site_description" => "Premium Games, Apps and eFootball Tips.",
    "contact_phone" => "0627041240",
    "whatsapp" => "0627041240",
    "youtube" => "https://youtube.com/@millzjasper",
    "access_duration" => "24",
    "payment_methods" => "HaloPesa: 0627041240"
];

// Save settings
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $site_name = trim($_POST["site_name"] ?? "MILLZ GAMES");
    $site_description = trim($_POST["site_description"] ?? "");
    $contact_phone = trim($_POST["contact_phone"] ?? "");
    $whatsapp = trim($_POST["whatsapp"] ?? "");
    $youtube = trim($_POST["youtube"] ?? "");
    $access_duration = trim($_POST["access_duration"] ?? "24");

    // Payment method moja tu
    $payment_methods = "HaloPesa: 0627041240";

    $settings = [
        "site_name" => $site_name,
        "site_description" => $site_description,
        "contact_phone" => $contact_phone,
        "whatsapp" => $whatsapp,
        "youtube" => $youtube,
        "access_duration" => $access_duration,
        "payment_methods" => $payment_methods
    ];

    try {

        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value)
            VALUES (:setting_key, :setting_value)
            ON DUPLICATE KEY UPDATE
            setting_value = VALUES(setting_value)
        ");

        foreach ($settings as $key => $value) {
            $stmt->execute([
                ":setting_key" => $key,
                ":setting_value" => $value
            ]);
        }

        $message = "Settings saved successfully 🔥";

    } catch (PDOException $e) {
        $error = "Failed to save settings: " . $e->getMessage();
    }
}


// Read current settings
$current = $defaults;

try {

    $stmt = $pdo->query("
        SELECT setting_key, setting_value
        FROM settings
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if (array_key_exists($row["setting_key"], $current)) {
            $current[$row["setting_key"]] = $row["setting_value"];
        }
    }

} catch (PDOException $e) {
    $error = "Failed to load settings: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Settings | MILLZ GAMES</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #070b09;
    color: #fff;
    font-family: Arial, sans-serif;
}

.header {
    background: #0c120f;
    border-bottom: 1px solid #00ff88;
    padding: 18px 6%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: #00ff88;
    font-size: 24px;
    font-weight: 900;
    text-shadow: 0 0 12px #00ff88;
}

.back {
    color: #00ff88;
    text-decoration: none;
    font-weight: bold;
}

.container {
    width: min(850px, 92%);
    margin: 40px auto;
}

.title {
    margin-bottom: 25px;
}

.title h1 {
    margin: 0;
    color: #00ff88;
}

.title p {
    color: #8f9b95;
}

.form-box {
    background: #101713;
    border: 1px solid #20372b;
    border-radius: 16px;
    padding: 28px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input,
textarea {
    width: 100%;
    padding: 13px;
    background: #080d0a;
    color: #fff;
    border: 1px solid #294536;
    border-radius: 9px;
    font-size: 15px;
    outline: none;
}

textarea {
    min-height: 110px;
    resize: vertical;
}

input:focus,
textarea:focus {
    border-color: #00ff88;
}

.save-btn {
    width: 100%;
    border: none;
    padding: 15px;
    background: #00ff88;
    color: #00150b;
    border-radius: 10px;
    font-weight: 900;
    font-size: 16px;
    cursor: pointer;
}

.save-btn:hover {
    box-shadow: 0 0 20px rgba(0,255,136,.35);
}

.success {
    background: #092719;
    border: 1px solid #00ff88;
    color: #00ff88;
    padding: 13px;
    border-radius: 9px;
    margin-bottom: 20px;
}

.error {
    background: #2a1010;
    border: 1px solid #ff5555;
    color: #ff7777;
    padding: 13px;
    border-radius: 9px;
    margin-bottom: 20px;
}

.payment-box {
    background: #091c13;
    border: 1px solid #00ff88;
    padding: 18px;
    border-radius: 10px;
    margin-bottom: 25px;
}

.payment-box strong {
    color: #00ff88;
}

.note {
    color: #78847e;
    font-size: 13px;
    margin-top: 7px;
}

@media (max-width: 600px) {

    .header {
        padding: 15px;
    }

    .container {
        width: 94%;
        margin: 25px auto;
    }

    .form-box {
        padding: 20px;
    }

    .logo {
        font-size: 20px;
    }

}

</style>

</head>

<body>

<div class="header">

    <div class="logo">
        MILLZ GAMES
    </div>

    <a href="index.php" class="back">
        ← Admin Dashboard
    </a>

</div>


<div class="container">

    <div class="title">

        <h1>⚙️ Website Settings</h1>

        <p>
            Manage MILLZ GAMES contact, WhatsApp, YouTube and payment settings.
        </p>

    </div>


    <?php if ($message): ?>

        <div class="success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <div class="payment-box">

        <strong>💰 Active Payment Method</strong>

        <br><br>

        HaloPesa:
        <strong>0627041240</strong>

        <div class="note">
            Other payment methods have been removed.
        </div>

    </div>


    <form method="POST" class="form-box">


        <div class="form-group">

            <label>Website Name</label>

            <input
                type="text"
                name="site_name"
                value="<?= htmlspecialchars($current["site_name"]) ?>"
                required
            >

        </div>


        <div class="form-group">

            <label>Website Description</label>

            <textarea
                name="site_description"
            ><?= htmlspecialchars($current["site_description"]) ?></textarea>

        </div>


        <div class="form-group">

            <label>Contact Phone</label>

            <input
                type="text"
                name="contact_phone"
                value="0627041240"
            >

        </div>


        <div class="form-group">

            <label>WhatsApp Number</label>

            <input
                type="text"
                name="whatsapp"
                value="0627041240"
            >

            <div class="note">
                This number will be used for WhatsApp contact.
            </div>

        </div>


        <div class="form-group">

            <label>YouTube Channel</label>

            <input
                type="url"
                name="youtube"
                value="<?= htmlspecialchars($current["youtube"]) ?>"
            >

        </div>


        <div class="form-group">

            <label>Access Duration (Hours)</label>

            <input
                type="number"
                name="access_duration"
                min="1"
                value="<?= htmlspecialchars($current["access_duration"]) ?>"
            >

        </div>


        <button
            type="submit"
            class="save-btn"
        >
            💾 SAVE SETTINGS
        </button>

    </form>

</div>

</body>
</html>