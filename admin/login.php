<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = $_POST["password"] ?? "";

    if (hash_equals("MILLZ005", $password)) {

        session_regenerate_id(true);

        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_login_time"] = time();

        header("Location: index.php");
        exit;

    } else {
        $error = "HEY, DON'T TRY ME! WRONG PASSWORD. 💀";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MILLZ GAMES | Admin Login</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background:
        radial-gradient(circle at top, #123d2b, #070a0c 60%);
    color: white;
    font-family: Arial, sans-serif;
    padding: 20px;
}

.login-box {
    width: 100%;
    max-width: 420px;
    background: #101619;
    border: 1px solid #24302e;
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 0 35px rgba(0,255,136,.08);
}

.logo {
    text-align: center;
    color: #00ff88;
    font-size: 30px;
    font-weight: 900;
    margin-bottom: 8px;
}

.title {
    text-align: center;
    color: #aaa;
    margin-bottom: 30px;
}

label {
    display: block;
    margin-bottom: 8px;
    color: #ddd;
    font-size: 14px;
}

input {
    width: 100%;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #293437;
    background: #080b0d;
    color: white;
    outline: none;
    font-size: 16px;
    margin-bottom: 18px;
}

input:focus {
    border-color: #00ff88;
}

button {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 10px;
    background: #00ff88;
    color: #06100b;
    font-size: 16px;
    font-weight: 800;
    cursor: pointer;
}

button:hover {
    opacity: .9;
}

.error {
    background: rgba(255,50,50,.12);
    border: 1px solid #ff5555;
    color: #ff7777;
    padding: 13px;
    border-radius: 10px;
    margin-bottom: 18px;
    text-align: center;
    font-size: 14px;
}

.security {
    text-align: center;
    color: #666;
    font-size: 12px;
    margin-top: 22px;
}
</style>
</head>

<body>

<div class="login-box">

    <div class="logo">MILLZ GAMES</div>

    <div class="title">
        ADMIN CONTROL PANEL
    </div>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="password">
            Admin Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter admin password"
            autocomplete="current-password"
            required
        >

        <button type="submit">
            LOGIN TO ADMIN
        </button>

    </form>

    <div class="security">
        🔒 Authorized access only
    </div>

</div>

</body>
</html>