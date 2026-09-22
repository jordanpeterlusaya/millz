<?php
require_once "config/database.php";
require_once "includes/helpers.php";

$category = isset($_GET["category"]) ? trim($_GET["category"]) : "";
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

$gameCategories = [];
$games = [];

try {
    $gameCategories = $pdo->query("
        SELECT id, name
        FROM categories
        WHERE type = 'game'
        ORDER BY name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $sql = "
        SELECT g.*, c.name AS category_name
        FROM games g
        LEFT JOIN categories c ON g.category_id = c.id
        WHERE g.status = 'published'
    ";
    $params = [];

    if ($category !== "" && is_numeric($category)) {
        $sql .= " AND g.category_id = ?";
        $params[] = (int)$category;
    }

    if ($search !== "") {
        $sql .= " AND (g.name LIKE ? OR g.description LIKE ?)";
        $params[] = "%" . $search . "%";
        $params[] = "%" . $search . "%";
    }

    $sql .= " ORDER BY g.featured DESC, g.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $games = [];
}

$pageTitle = "Games | MILLZ GAMES";
$current = "games";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require "includes/site-head.php"; ?>
</head>
<body>
<?php require "includes/site-header.php"; ?>

<section class="page-hero" style="--page-image:url('assets/img/banner-esports.jpg');">
    <div class="container">
        <div class="section-kicker">Catalog</div>
        <h1>Shop <span>games</span></h1>
        <p>Browse titles, filter by category, then unlock your download with an access code.</p>
    </div>
</section>

<main class="page-wrap">
    <form class="page-search" method="GET" action="games.php" style="margin-bottom:24px;">
        <input type="text" name="search" placeholder="Search games..." value="<?= htmlspecialchars($search) ?>">
        <?php if ($category !== ""): ?>
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
        <?php endif; ?>
        <button type="submit">Search</button>
    </form>

    <div class="category-list">
        <a href="games.php" class="category <?= $category === "" ? "active" : "" ?>">All Games</a>
        <?php foreach ($gameCategories as $cat): ?>
            <a href="games.php?category=<?= (int)$cat["id"] ?>" class="category <?= $category == $cat["id"] ? "active" : "" ?>">
                <?= htmlspecialchars($cat["name"]) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (count($games) > 0): ?>
        <div class="products-grid">
            <?php foreach ($games as $item): $kind = "game"; require "includes/product-card.php"; endforeach; ?>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach (millz_showcase_games() as $drop):
                $item = [
                    "name" => $drop["name"],
                    "cover_src" => $drop["image"],
                    "category_name" => $drop["tag"],
                    "platform" => "Android / Windows",
                    "price" => null,
                ];
                $kind = "game";
                require "includes/product-card.php";
            endforeach; ?>
        </div>
        <div class="empty" style="margin-top:18px;">Admin published games appear here. BUY NOW still works for the featured titles.</div>
    <?php endif; ?>
</main>

<?php require "includes/site-footer.php"; ?>
</body>
</html>
