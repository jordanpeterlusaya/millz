<?php
require_once "auth.php";
require_once "../config/database.php";

$games_count = 0;
$apps_count = 0;
$customers_count = 0;
$messages_count = 0;
$requests_count = 0;
$payments_count = 0;
$access_codes_count = 0;
$featured_games_count = 0;

try {
    $games_count = (int)$pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();

    $featured_games_count = (int)$pdo
        ->query("SELECT COUNT(*) FROM games WHERE featured = 1")
        ->fetchColumn();

    $apps_count = (int)$pdo->query("SELECT COUNT(*) FROM apps")->fetchColumn();

    $customers_count = (int)$pdo
        ->query("SELECT COUNT(*) FROM customers")
        ->fetchColumn();

    $messages_count = (int)$pdo
        ->query("SELECT COUNT(*) FROM messages")
        ->fetchColumn();

    $requests_count = (int)$pdo
        ->query("SELECT COUNT(*) FROM game_requests")
        ->fetchColumn();

    $payments_count = (int)$pdo
        ->query("SELECT COUNT(*) FROM payments")
        ->fetchColumn();

    $access_codes_count = (int)$pdo
        ->query("SELECT COUNT(*) FROM access_codes")
        ->fetchColumn();

} catch (PDOException $e) {
    // Keep dashboard running
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MILLZ GAMES - Admin Dashboard</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #080b0d;
    color: #fff;
    min-height: 100vh;
}

a {
    text-decoration: none;
    color: inherit;
}

.layout {
    display: flex;
    min-height: 100vh;
}

/* ================= SIDEBAR ================= */

.sidebar {
    width: 250px;
    background: #0d1114;
    border-right: 1px solid #1b252a;
    padding: 25px 15px;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    overflow-y: auto;
}

.logo {
    text-align: center;
    margin-bottom: 30px;
}

.logo-icon {
    width: 55px;
    height: 55px;
    margin: 0 auto 10px;
    border: 2px solid #00ff88;
    border-radius: 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #00ff88;
    font-size: 28px;
    font-weight: 900;

    box-shadow: 0 0 20px rgba(0,255,136,.25);
}

.logo h2 {
    color: #00ff88;
    font-size: 20px;
    letter-spacing: 1px;
}

.logo p {
    color: #718087;
    font-size: 11px;
    margin-top: 4px;
}

.menu-title {
    color: #526067;
    font-size: 10px;
    font-weight: bold;

    margin: 20px 10px 8px;

    text-transform: uppercase;
    letter-spacing: 1px;
}

.menu a {
    display: block;

    padding: 12px 14px;
    margin-bottom: 5px;

    border-radius: 10px;

    color: #aab5ba;
    font-size: 14px;

    transition: .2s;
}

.menu a:hover {
    background: #14201b;
    color: #00ff88;
}

.menu a.active {
    background: #00ff88;
    color: #06110b;
    font-weight: bold;
}

.logout {
    margin-top: 20px;
    border: 1px solid #3b2525;
    color: #ff7070 !important;
}

.logout:hover {
    background: #321818 !important;
    color: #ff9090 !important;
}

/* ================= MAIN ================= */

.main {
    margin-left: 250px;
    width: calc(100% - 250px);

    padding: 30px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 20px;
    margin-bottom: 30px;
}

.topbar h1 {
    font-size: 28px;
}

.topbar p {
    color: #718087;
    margin-top: 6px;
    font-size: 14px;
}

.admin-badge {
    border: 1px solid #1e3930;
    background: #0c1813;

    color: #00ff88;

    padding: 10px 15px;
    border-radius: 10px;

    font-size: 13px;
}

/* ================= STATS ================= */

.stats {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 18px;

    margin-bottom: 30px;
}

.stat-card {
    background: #0d1114;

    border: 1px solid #1b252a;

    border-radius: 15px;

    padding: 22px;

    transition: .2s;
}

.stat-card:hover {
    border-color: #284b3d;
}

.stat-icon {
    font-size: 25px;
    margin-bottom: 15px;
}

.stat-card h3 {
    font-size: 28px;
    margin-bottom: 5px;
}

.stat-card p {
    color: #718087;
    font-size: 13px;
}

/* ================= QUICK ACTIONS ================= */

.section-title {
    font-size: 20px;
    margin-bottom: 15px;
}

.quick-actions {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 15px;

    margin-bottom: 30px;
}

.quick-card {
    background: #0d1114;

    border: 1px solid #1b252a;

    border-radius: 14px;

    padding: 20px;

    transition: .2s;
}

.quick-card:hover {
    transform: translateY(-2px);
    border-color: #00ff88;
}

.quick-card .icon {
    font-size: 28px;
    margin-bottom: 12px;
}

.quick-card h3 {
    font-size: 15px;
    margin-bottom: 6px;
}

.quick-card p {
    color: #718087;
    font-size: 12px;
}

/* ================= INFO ================= */

.info-box {
    background: linear-gradient(
        135deg,
        #0c1813,
        #0d1114
    );

    border: 1px solid #1c4031;

    border-radius: 15px;

    padding: 25px;

    margin-bottom: 25px;
}

.info-box h2 {
    color: #00ff88;

    margin-bottom: 10px;

    font-size: 20px;
}

.info-box p {
    color: #91a0a5;

    font-size: 14px;

    line-height: 1.7;
}

.status {
    display: inline-block;

    margin-top: 15px;

    padding: 8px 12px;

    background: #10251b;

    color: #00ff88;

    border-radius: 8px;

    font-size: 12px;
}

/* ================= TABLET ================= */

@media (max-width: 1000px) {

    .sidebar {
        width: 210px;
    }

    .main {
        margin-left: 210px;
        width: calc(100% - 210px);
    }

    .stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .quick-actions {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* ================= MOBILE ================= */

@media (max-width: 700px) {

    .layout {
        display: block;
    }

    .sidebar {
        position: relative;

        width: 100%;

        height: auto;

        border-right: none;

        border-bottom: 1px solid #1b252a;
    }

    .main {
        margin-left: 0;

        width: 100%;

        padding: 20px 15px;
    }

    .topbar {
        flex-direction: column;

        align-items: flex-start;
    }

    .topbar h1 {
        font-size: 24px;
    }

    .stats {
        grid-template-columns: 1fr 1fr;

        gap: 10px;
    }

    .stat-card {
        padding: 16px;
    }

    .stat-card h3 {
        font-size: 23px;
    }

    .quick-actions {
        grid-template-columns: 1fr 1fr;

        gap: 10px;
    }

    .quick-card {
        padding: 15px;
    }
}

@media (max-width: 430px) {

    .stats {
        grid-template-columns: 1fr;
    }

    .quick-actions {
        grid-template-columns: 1fr;
    }
}

</style>

</head>

<body>

<div class="layout">

<!-- ================= SIDEBAR ================= -->

<aside class="sidebar">

    <div class="logo">

        <div class="logo-icon">
            M
        </div>

        <h2>
            MILLZ GAMES
        </h2>

        <p>
            ADMIN PANEL
        </p>

    </div>


    <div class="menu">

        <!-- MAIN -->

        <div class="menu-title">
            Main
        </div>

        <a href="index.php" class="active">
            📊 Dashboard
        </a>


        <!-- STORE -->

        <div class="menu-title">
            Store
        </div>

        <a href="games.php">
            🎮 Games
        </a>

        <a href="../apps.php">
            📱 Apps
        </a>

        <a href="../categories.php">
            🗂️ Categories
        </a>


        <!-- CONTENT -->

        <div class="menu-title">
            Content
        </div>

        <a href="add-game.php">
            ➕ Add Game
        </a>

        <a href="add-app.php">
            ➕ Add App
        </a>

        <a href="../efootball-tips.php">
            ⚽ eFootball Tips
        </a>


        <!-- CUSTOMERS -->

        <div class="menu-title">
            Customers
        </div>

        <a href="customers.php">
            👥 Customers
        </a>

        <a href="requests.php">
            📩 Requests
        </a>

        <a href="messages.php">
            💬 Messages
        </a>

        <a href="../contact.php">
            📞 Contact
        </a>


        <!-- BUSINESS -->

        <div class="menu-title">
            Business
        </div>

        <a href="payments.php">
            💰 Payments
        </a>

        <a href="sales.php">
            🛒 Sales
        </a>

        <a href="access-codes.php">
            🔐 Access Codes
        </a>


        <!-- SYSTEM -->

        <div class="menu-title">
            System
        </div>

        <a href="settings.php">
            ⚙️ Settings
        </a>

        <a href="logout.php" class="logout">
            🚪 Logout
        </a>

    </div>

</aside>


<!-- ================= MAIN ================= -->

<main class="main">


    <div class="topbar">

        <div>

            <h1>
                Dashboard
            </h1>

            <p>
                Welcome back to your MILLZ GAMES control center.
            </p>

        </div>


        <div class="admin-badge">
            🔐 Admin Protected
        </div>

    </div>


    <!-- ================= STATS ================= -->

    <div class="stats">


        <div class="stat-card">

            <div class="stat-icon">
                🎮
            </div>

            <h3>
                <?= $games_count ?>
            </h3>

            <p>
                Total Games
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                📱
            </div>

            <h3>
                <?= $apps_count ?>
            </h3>

            <p>
                Total Apps
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                ⭐
            </div>

            <h3>
                <?= $featured_games_count ?>
            </h3>

            <p>
                Featured Games
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                👥
            </div>

            <h3>
                <?= $customers_count ?>
            </h3>

            <p>
                Customers
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                💬
            </div>

            <h3>
                <?= $messages_count ?>
            </h3>

            <p>
                Messages
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                📩
            </div>

            <h3>
                <?= $requests_count ?>
            </h3>

            <p>
                Game Requests
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                💰
            </div>

            <h3>
                <?= $payments_count ?>
            </h3>

            <p>
                Payments
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                🔐
            </div>

            <h3>
                <?= $access_codes_count ?>
            </h3>

            <p>
                Access Codes
            </p>

        </div>

    </div>


    <!-- ================= QUICK ACTIONS ================= -->

    <h2 class="section-title">
        Quick Actions
    </h2>


    <div class="quick-actions">


        <a href="add-game.php" class="quick-card">

            <div class="icon">
                🎮
            </div>

            <h3>
                Add New Game
            </h3>

            <p>
                Add a game to MILLZ GAMES.
            </p>

        </a>


        <a href="add-app.php" class="quick-card">

            <div class="icon">
                📱
            </div>

            <h3>
                Add New App
            </h3>

            <p>
                Add an application to the store.
            </p>

        </a>


        <a href="access-codes.php" class="quick-card">

            <div class="icon">
                🔐
            </div>

            <h3>
                Create Access Code
            </h3>

            <p>
                Generate customer download codes.
            </p>

        </a>


        <a href="requests.php" class="quick-card">

            <div class="icon">
                📩
            </div>

            <h3>
                Game Requests
            </h3>

            <p>
                Check games requested by customers.
            </p>

        </a>

    </div>


    <!-- ================= INFO ================= -->

    <div class="info-box">

        <h2>
            🔥 MILLZ GAMES Admin Center
        </h2>

        <p>
            Manage games, apps, customers, payments, sales,
            access codes, requests and website settings from
            this control panel.
        </p>

        <span class="status">
            ● System Online
        </span>

    </div>


</main>

</div>

</body>
</html>