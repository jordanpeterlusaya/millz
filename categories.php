<?php
require_once "config/database.php";

$categories = [];
try {
    $stmt = $pdo->query("
        SELECT
            c.id,
            c.name,
            COUNT(g.id) AS game_count
        FROM categories c
        LEFT JOIN games g ON g.category_id = c.id
        GROUP BY c.id, c.name
        ORDER BY c.name ASC
    ");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

$pageTitle = "Categories | MILLZ GAMES";
$current = "categories";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require "includes/site-head.php"; ?>
<style>
.cat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
.cat-card { background:#0c1112; border:1px solid #1d2828; border-radius:16px; padding:28px 18px; text-align:center; transition:.25s; }
.cat-card:hover { transform:translateY(-4px); border-color:rgba(0,255,136,.35); }
.cat-card i { color:#00ff88; font-size:28px; margin-bottom:12px; }
.cat-card h3 { font-family:Syne,sans-serif; font-size:16px; margin-bottom:8px; }
.cat-card span { color:#00ff88; font-weight:800; }
@media (max-width:900px){ .cat-grid { grid-template-columns:repeat(2,1fr); } }
</style>
</head>
<body>
<?php require "includes/site-header.php"; ?>

<section class="page-hero" style="--page-image:url('assets/img/banner-crowd.jpg');">
    <div class="container">
        <div class="section-kicker">Browse</div>
        <h1>Game <span>categories</span></h1>
        <p>Choose a category and open the matching titles in the catalog.</p>
    </div>
</section>

<main class="page-wrap">
    <?php if (!empty($categories)): ?>
        <div class="cat-grid">
            <?php foreach ($categories as $category): ?>
                <a href="games.php?category=<?= (int)$category['id'] ?>" class="cat-card">
                    <i class="fa-solid fa-gamepad"></i>
                    <h3><?= htmlspecialchars($category['name']) ?></h3>
                    <span><?= (int)$category['game_count'] ?> Games</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">Categories will appear here once they are added.</div>
    <?php endif; ?>
</main>

<?php require "includes/site-footer.php"; ?>
</body>
</html>
