<?php
require_once "config/database.php";
require_once "includes/helpers.php";

$category = isset($_GET["category"]) ? trim($_GET["category"]) : "";
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

$appCategories = [];
$apps = [];

try {
    $appCategories = $pdo->query("
        SELECT id, name
        FROM categories
        WHERE type = 'app'
        ORDER BY name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $sql = "
        SELECT a.*, c.name AS category_name
        FROM apps a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.status = 'published'
    ";
    $params = [];

    if ($category !== "" && is_numeric($category)) {
        $sql .= " AND a.category_id = ?";
        $params[] = (int)$category;
    }

    if ($search !== "") {
        $sql .= " AND (a.name LIKE ? OR a.description LIKE ?)";
        $params[] = "%" . $search . "%";
        $params[] = "%" . $search . "%";
    }

    $sql .= " ORDER BY a.featured DESC, a.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $apps = [];
}

$pageTitle = "Apps | MILLZ GAMES";
$current = "apps";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require "includes/site-head.php"; ?>
</head>
<body>
<?php require "includes/site-header.php"; ?>

<section class="page-hero" style="--page-image:url('assets/img/banner-neon.jpg');">
    <div class="container">
        <div class="section-kicker">Catalog</div>
        <h1>Shop <span>apps</span></h1>
        <p>Tools and apps. Search, filter, then unlock with your access code.</p>
    </div>
</section>

<main class="page-wrap">
    <form class="page-search" method="GET" action="apps.php" style="margin-bottom:24px;">
        <input type="text" name="search" placeholder="Search apps..." value="<?= htmlspecialchars($search) ?>">
        <?php if ($category !== ""): ?>
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
        <?php endif; ?>
        <button type="submit">Search</button>
    </form>

    <div class="category-list">
        <a href="apps.php" class="category <?= $category === "" ? "active" : "" ?>">All Apps</a>
        <?php foreach ($appCategories as $cat): ?>
            <a href="apps.php?category=<?= (int)$cat["id"] ?>" class="category <?= $category == $cat["id"] ? "active" : "" ?>">
                <?= htmlspecialchars($cat["name"]) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (count($apps) > 0): ?>
        <div class="products-grid">
            <?php foreach ($apps as $item): $kind = "app"; require "includes/product-card.php"; endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">No apps found in this vault. Apps published by admin appear here with BUY NOW and GET ACCESS.</div>
    <?php endif; ?>
</main>

<?php require "includes/site-footer.php"; ?>
</body>
</html>
