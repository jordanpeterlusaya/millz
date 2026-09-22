<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MILLZ GAMES</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #0f172a; color: #f8fafc; display: flex; flex-direction: column; min-height: 100vh; }
        header { background-color: #1e293b; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #334155; }
        .logo { font-size: 1.5rem; font-weight: bold; color: #38bdf8; text-decoration: none; }
        nav a { color: #cbd5e1; text-decoration: none; margin-left: 1.5rem; transition: 0.3s; font-weight: 500; }
        nav a:hover { color: #38bdf8; }
        main { flex: 1; padding: 2rem; }
    </style>
</head>
<body>

<header>
    <a href="index.php" class="logo">MILLZ GAMES</a>
    <nav>
        <a href="index.php">Mwanzo</a>
        <a href="games.php">Michezo</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Account</a>
            <a href="logout.php" style="color: #ef4444;">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main>