<?php

require_once "auth.php";
require_once "../config/database.php";

/* =========================
   GET CATEGORY
========================= */

$category = isset($_GET["category"])
    ? trim($_GET["category"])
    : "";

/* =========================
   GET SEARCH
========================= */

$search = isset($_GET["search"])
    ? trim($_GET["search"])
    : "";

/* =========================
   FETCH CATEGORIES
========================= */

$categoryStmt = $pdo->query("
    SELECT id, name
    FROM categories
    WHERE type = 'game'
    ORDER BY name ASC
");

$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FETCH GAMES
========================= */

$sql = "
    SELECT
        g.*,
        c.name AS category_name
    FROM games g
    LEFT JOIN categories c
        ON g.category_id = c.id
    WHERE g.status = 'published'
";

$params = [];

if ($category !== "") {
    $sql .= " AND c.id = ?";
    $params[] = (int)$category;
}

if ($search !== "") {
    $sql .= " AND g.name LIKE ?";
    $params[] = "%" . $search . "%";
}

$sql .= "
    ORDER BY g.featured DESC, g.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Games | MILLZ GAMES</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #080b0f;
            color: #ffffff;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* =========================
           HEADER
        ========================= */

        header {
            background: #0d1218;
            border-bottom: 1px solid #1d2933;
            padding: 18px 5%;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav {
            max-width: 1200px;
            margin: auto;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: 900;
            color: #00ff88;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: #c8d0d8;
            font-size: 14px;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #00ff88;
        }

        /* =========================
           HERO
        ========================= */

        .hero {
            max-width: 1200px;
            margin: 35px auto;
            padding: 55px 25px;

            border-radius: 20px;

            background:
                linear-gradient(
                    rgba(0, 0, 0, .65),
                    rgba(0, 0, 0, .75)
                ),
                linear-gradient(
                    135deg,
                    #123,
                    #071b12
                );

            text-align: center;
        }

        .hero h1 {
            font-size: 42px;
            margin-bottom: 12px;
        }

        .hero h1 span {
            color: #00ff88;
        }

        .hero p {
            color: #b9c2ca;
            max-width: 650px;
            margin: auto;
            line-height: 1.6;
        }

        /* =========================
           SEARCH
        ========================= */

        .search-box {
            max-width: 1200px;
            margin: 0 auto 25px;
            padding: 0 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        .search-form input {
            flex: 1;

            padding: 15px;

            border-radius: 10px;
            border: 1px solid #26333d;

            background: #10161c;
            color: white;

            outline: none;
        }

        .search-form input:focus {
            border-color: #00ff88;
        }

        .search-form button {
            border: none;
            border-radius: 10px;

            padding: 0 25px;

            background: #00ff88;
            color: #00170d;

            font-weight: bold;
            cursor: pointer;
        }

        /* =========================
           CATEGORIES
        ========================= */

        .categories {
            max-width: 1200px;
            margin: 0 auto 35px;
            padding: 0 20px;
        }

        .categories h2 {
            margin-bottom: 15px;
        }

        .category-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .category-btn {
            padding: 10px 15px;

            background: #10161c;

            border: 1px solid #26333d;
            border-radius: 30px;

            color: #cbd3da;
            font-size: 13px;
        }

        .category-btn:hover,
        .category-btn.active {
            background: #00ff88;
            color: #00170d;
            border-color: #00ff88;
        }

        /* =========================
           GAMES
        ========================= */

        .games-section {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px 60px;
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 20px;
        }

        .section-title h2 {
            font-size: 28px;
        }

        .count {
            color: #8e9aa4;
            font-size: 14px;
        }

        .games-grid {
            display: grid;

            grid-template-columns:
                repeat(auto-fill, minmax(220px, 1fr));

            gap: 20px;
        }

        .game-card {
            background: #10161c;

            border: 1px solid #1e2a33;
            border-radius: 16px;

            overflow: hidden;

            transition: .25s;
        }

        .game-card:hover {
            transform: translateY(-5px);
            border-color: #00ff88;
        }

        .game-cover {
            width: 100%;
            height: 240px;

            object-fit: cover;
            display: block;

            background: #151c23;
        }

        .game-info {
            padding: 16px;
        }

        .game-info h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .category {
            color: #00ff88;
            font-size: 12px;
            margin-bottom: 7px;
        }

        .platform {
            color: #89959f;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .price {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 14px;
        }

        .game-btn {
            display: block;
            width: 100%;

            text-align: center;

            padding: 12px;

            border-radius: 9px;

            background: #00ff88;
            color: #00170d;

            font-weight: bold;
            font-size: 13px;
        }

        .featured-badge {
            display: inline-block;

            background: #ffd000;
            color: #111;

            padding: 4px 8px;

            border-radius: 5px;

            font-size: 10px;
            font-weight: bold;

            margin-bottom: 9px;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty {
            text-align: center;

            padding: 60px 20px;

            background: #10161c;

            border-radius: 15px;

            color: #9aa5ae;
        }

        /* =========================
           FOOTER
        ========================= */

        footer {
            border-top: 1px solid #1c2730;

            padding: 25px 20px;

            text-align: center;

            color: #77838d;

            background: #070a0e;
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 700px) {

            .nav {
                flex-direction: column;
            }

            .nav-links {
                justify-content: center;
                gap: 14px;
            }

            .hero {
                margin: 20px;
                padding: 40px 20px;
            }

            .hero h1 {
                font-size: 30px;
            }

            .search-form {
                flex-direction: column;
            }

            .search-form button {
                padding: 13px;
            }

            .games-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 12px;
            }

            .game-cover {
                height: 190px;
            }

            .game-info {
                padding: 12px;
            }

            .game-info h3 {
                font-size: 15px;
            }

        }

    </style>

</head>

<body>

<header>

    <div class="nav">

        <a href="../index.php" class="logo">
            MILLZ GAMES
        </a>

        <div class="nav-links">

            <a href="index.php">
                Dashboard
            </a>

            <a href="games.php" class="active">
                Games
            </a>

            <a href="../apps.php">
                Apps
            </a>

            <!-- ROOT CATEGORIES -->
            <a href="../categories.php">
                Categories
            </a>

            <a href="../efootball-tips.php">
                eFootball Tips
            </a>

            <a href="../contact.php">
                Contact
            </a>

            <a href="logout.php">
                Logout
            </a>

        </div>

    </div>

</header>


<section class="hero">

    <h1>
        MILLZ <span>GAMES</span>
    </h1>

    <p>
        Admin Games Management
    </p>

</section>


<div class="search-box">

    <form
        method="GET"
        action="games.php"
        class="search-form"
    >

        <input
            type="text"
            name="search"
            placeholder="Search games..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <button type="submit">
            SEARCH
        </button>

    </form>

</div>


<section class="categories">

    <h2>
        Game Categories
    </h2>

    <div class="category-list">

        <a
            href="games.php"
            class="category-btn <?= $category === '' ? 'active' : '' ?>"
        >
            All
        </a>


        <?php foreach ($categories as $cat): ?>

            <a
                href="games.php?category=<?= (int)$cat['id'] ?>"
                class="category-btn <?= $category == $cat['id'] ? 'active' : '' ?>"
            >

                <?= htmlspecialchars($cat['name']) ?>

            </a>

        <?php endforeach; ?>

    </div>

</section>


<section class="games-section">

    <div class="section-title">

        <h2>
            Games
        </h2>

        <span class="count">
            <?= count($games) ?> games
        </span>

    </div>


    <?php if (empty($games)): ?>

        <div class="empty">

            <h3>
                No games found
            </h3>

            <p>
                Try another search or category.
            </p>

        </div>

    <?php else: ?>

        <div class="games-grid">

            <?php foreach ($games as $game): ?>

                <div class="game-card">

                    <?php

                    $cover = !empty($game["cover_image"])
                        ? "../uploads/covers/" . $game["cover_image"]
                        : "";

                    ?>

                    <?php if ($cover): ?>

                        <img
                            src="<?= htmlspecialchars($cover) ?>"
                            alt="<?= htmlspecialchars($game["name"]) ?>"
                            class="game-cover"
                        >

                    <?php else: ?>

                        <div
                            class="game-cover"
                            style="
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                color:#66727d;
                            "
                        >
                            NO COVER
                        </div>

                    <?php endif; ?>


                    <div class="game-info">

                        <?php if (!empty($game["featured"])): ?>

                            <span class="featured-badge">
                                ⭐ FEATURED
                            </span>

                        <?php endif; ?>


                        <h3>
                            <?= htmlspecialchars($game["name"]) ?>
                        </h3>


                        <div class="category">

                            <?= htmlspecialchars(
                                $game["category_name"]
                                ?? "Uncategorized"
                            ) ?>

                        </div>


                        <div class="platform">

                            Platform:

                            <?= htmlspecialchars(
                                $game["platform"]
                                ?: "Not specified"
                            ) ?>

                        </div>


                        <div class="price">

                            <?php if (
                                $game["price"] !== null &&
                                $game["price"] !== ""
                            ): ?>

                                TSh
                                <?= number_format(
                                    (float)$game["price"]
                                ) ?>

                            <?php else: ?>

                                Price: Set by Admin

                            <?php endif; ?>

                        </div>


                        <a
                            href="../access.php"
                            class="game-btn"
                        >
                            🔐 GET ACCESS
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>


<footer>

    © <?= date("Y") ?> MILLZ GAMES.
    All rights reserved.

</footer>

</body>

</html>