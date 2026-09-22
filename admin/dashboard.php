<?php
session_start();

// Security Check: Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include Database Connection
require_once '../config/database.php';

// Fetch quick statistics from database
$total_games = 0;
$total_apps = 0;

$res_games = $conn->query("SELECT COUNT(*) AS total FROM products WHERE category='game'");
if ($res_games) {
    $total_games = $res_games->fetch_assoc()['total'];
}

$res_apps = $conn->query("SELECT COUNT(*) AS total FROM products WHERE category='app'");
if ($res_apps) {
    $total_apps = $res_apps->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MILLZ GAMES</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #0b0c10;
            color: #c5c6c7;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #1f2833;
            padding: 20px;
            border-right: 1px solid #334155;
        }

        .sidebar h2 {
            color: #fff;
            font-size: 22px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar h2 span {
            color: #39ff14;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: #c5c6c7;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: #39ff14;
            color: #000;
            font-weight: bold;
        }

        /* Main Content Styling */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #334155;
        }

        .header h1 {
            color: #fff;
            font-size: 26px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #1f2833;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #334155;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .card i {
            font-size: 40px;
            color: #39ff14;
        }

        .card-info h3 {
            color: #fff;
            font-size: 24px;
        }

        .card-info p {
            color: #66fcf1;
            font-size: 14px;
        }

        /* Quick Action Buttons */
        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-action {
            background-color: #39ff14;
            color: #000;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-action:hover {
            background-color: #32cd12;
            box-shadow: 0 0 10px rgba(57, 255, 20, 0.4);
        }

        .btn-logout {
            background-color: #ff4d4d;
            color: #fff;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-logout:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>MILLZ <span>GAMES</span></h2>
        <ul>
            <li><a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="upload.php"><i class="fas fa-cloud-upload-alt"></i> Upload App / Game</a></li>
            <li><a href="manage.php"><i class="fas fa-tasks"></i> Manage Products</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="logout.php" class="btn-logout"><i class="fas fa-power-off"></i> Logout</a>
        </div>

        <!-- Overview Statistics -->
        <div class="stats-grid">
            <div class="card">
                <i class="fas fa-gamepad"></i>
                <div class="card-info">
                    <h3><?= $total_games ?></h3>
                    <p>Total Games</p>
                </div>
            </div>

            <div class="card">
                <i class="fas fa-mobile-alt"></i>
                <div class="card-info">
                    <h3><?= $total_apps ?></h3>
                    <p>Total Apps</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 style="color: #fff; margin-bottom: 15px;">Quick Actions</h2>
        <div class="actions">
            <a href="upload.php" class="btn-action"><i class="fas fa-plus-circle"></i> Upload New Product</a>
            <a href="../index.php" target="_blank" class="btn-action" style="background-color: #66fcf1; color: #000;"><i class="fas fa-external-link-alt"></i> View Main Website</a>
        </div>
    </div>

</body>
</html>