<?php
require_once __DIR__ . "/helpers.php";

$item = $item ?? [];
$kind = $kind ?? "game";
$name = $item["name"] ?? "Untitled";
$cover = millz_cover($item);
$category = $item["category_name"] ?? ($item["tag"] ?? ucfirst($kind));
$platform = $item["platform"] ?? "Multi";
$price = $item["price"] ?? null;
$featured = !empty($item["featured"]);
$searchName = strtolower($name);
$buyUrl = millz_buy_url($name, $price, $kind);
$priceLabel = millz_price($price);
?>
<article
    class="product-card searchable <?= $kind === 'app' ? 'app-card' : 'game-card' ?>"
    data-name="<?= htmlspecialchars($searchName) ?>"
    data-category="<?= htmlspecialchars($category) ?>"
>
    <div class="product-image">
        <?php if ($cover !== ""): ?>
            <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy">
        <?php else: ?>
            <div class="no-image">NO COVER</div>
        <?php endif; ?>
        <?php if ($featured): ?>
            <div class="featured-badge">FEATURED</div>
        <?php endif; ?>
    </div>
    <div class="product-info">
        <h3><?= htmlspecialchars($name) ?></h3>
        <div class="meta"><i class="fa-solid fa-layer-group"></i> <?= htmlspecialchars($category) ?></div>
        <div class="meta"><i class="fa-solid fa-desktop"></i> <?= htmlspecialchars($platform) ?></div>
        <div class="price"><?= htmlspecialchars($priceLabel) ?></div>
        <div class="product-actions">
            <button
                type="button"
                class="product-btn buy"
                data-buy
                data-name="<?= htmlspecialchars($name, ENT_QUOTES) ?>"
                data-price="<?= htmlspecialchars($priceLabel, ENT_QUOTES) ?>"
                data-img="<?= htmlspecialchars($cover, ENT_QUOTES) ?>"
                data-kind="<?= htmlspecialchars($kind, ENT_QUOTES) ?>"
                data-wa="<?= htmlspecialchars($buyUrl, ENT_QUOTES) ?>"
            >Buy now</button>
            <a href="access.php" class="product-btn">Get access</a>
        </div>
    </div>
</article>
