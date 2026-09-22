<?php
session_start();
require_once "config/database.php";
require_once "includes/helpers.php";

$featuredGames = [];
$games = [];
$apps = [];
$gameCategories = [];
$appCategories = [];
$totalGames = 0;
$totalApps = 0;
$showcase = millz_showcase_games();
$socials = millz_socials();

try {
    $featuredStmt = $pdo->query("
        SELECT g.*, c.name AS category_name
        FROM games g
        LEFT JOIN categories c ON g.category_id = c.id
        WHERE g.status = 'published'
        AND g.featured = 1
        ORDER BY g.created_at DESC
        LIMIT 8
    ");
    $featuredGames = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);

    $gameStmt = $pdo->query("
        SELECT g.*, c.name AS category_name
        FROM games g
        LEFT JOIN categories c ON g.category_id = c.id
        WHERE g.status = 'published'
        ORDER BY g.created_at DESC
        LIMIT 12
    ");
    $games = $gameStmt->fetchAll(PDO::FETCH_ASSOC);

    $appStmt = $pdo->query("
        SELECT a.*, c.name AS category_name
        FROM apps a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.status = 'published'
        ORDER BY a.created_at DESC
        LIMIT 8
    ");
    $apps = $appStmt->fetchAll(PDO::FETCH_ASSOC);

    $gameCategories = $pdo->query("
        SELECT id, name
        FROM categories
        WHERE type = 'game'
        ORDER BY name
    ")->fetchAll(PDO::FETCH_ASSOC);

    $appCategories = $pdo->query("
        SELECT id, name
        FROM categories
        WHERE type = 'app'
        ORDER BY name
    ")->fetchAll(PDO::FETCH_ASSOC);

    $totalGames = (int)$pdo->query("SELECT COUNT(*) FROM games WHERE status = 'published'")->fetchColumn();
    $totalApps = (int)$pdo->query("SELECT COUNT(*) FROM apps WHERE status = 'published'")->fetchColumn();
} catch (PDOException $e) {
    $featuredGames = [];
    $games = [];
    $apps = [];
    $gameCategories = [];
    $appCategories = [];
}

$homeGames = $games;
if (empty($homeGames)) {
    foreach ($showcase as $drop) {
        $homeGames[] = [
            "name" => $drop["name"],
            "cover_src" => $drop["image"],
            "wide" => $drop["wide"],
            "category_name" => $drop["tag"],
            "platform" => "Android / Windows",
            "price" => null,
            "featured" => 0,
        ];
    }
}
$spotlight = $featuredGames[0] ?? $homeGames[0] ?? null;
$spotName = $spotlight["name"] ?? "Grand Theft Auto V";
$spotCover = $spotlight ? millz_cover($spotlight) : "assets/img/games/gta-v.jpg";
$spotWide = $spotlight["wide"] ?? $spotCover;
$spotTag = $spotlight["category_name"] ?? "Action";
$spotPrice = millz_price($spotlight["price"] ?? null);
$spotWa = millz_buy_url($spotName, $spotlight["price"] ?? null);
$catalogNote = empty($games);

$pageTitle = "MILLZ GAMES | Official store";
$current = "home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require "includes/site-head.php"; ?>
</head>
<body class="page-home">

<?php require "includes/site-header.php"; ?>

<section class="hero" id="home">
    <div class="hero-bg">
        <div class="hero-slide active" style="background-image:url('<?= htmlspecialchars($spotWide) ?>');"></div>
        <div class="hero-slide" style="background-image:url('assets/img/games/subway-wide.jpg');"></div>
        <div class="hero-slide" style="background-image:url('assets/img/games/efootball-wide.jpg');"></div>
        <div class="hero-slide" style="background-image:url('assets/img/games/fifa-wide.jpg');"></div>
        <div class="hero-slide" style="background-image:url('assets/img/games/pubg-wide.jpg');"></div>
    </div>

    <div class="hero-grid">
        <div>
            <div class="hero-identity">
                <img class="hero-face" src="assets/img/millz-portrait.png" alt="MILLZ JASPER">
                <div>
                    <div class="live-pill"><span class="live-dot"></span> Official store</div>
                    <span class="hero-owner">MILLZ JASPER</span>
                </div>
            </div>

            <h1>
                Shop games.
                <span>Pay. Unlock.</span>
            </h1>

            <p class="hero-lead">
                Choose a title, pay HaloPesa 0627041240, then unlock your download
                with an access code.
            </p>

            <div class="hero-buttons">
                <a href="#games" class="btn btn-primary">Browse catalog</a>
                <a href="efootball-tips.php" class="btn btn-outline">eFootball tips</a>
            </div>

            <div class="hero-meta">
                <div>
                    <strong><?= $totalGames > 0 ? $totalGames : count($homeGames) ?></strong>
                    Games
                </div>
                <div>
                    <strong><?= $totalApps > 0 ? $totalApps : "Apps" ?></strong>
                    Premium apps
                </div>
                <div>
                    <strong>24h</strong>
                    Access codes
                </div>
            </div>

            <div class="search-box hero-search">
                <input type="text" id="searchInput" placeholder="Search GTA, eFootball, Subway..." autocomplete="off">
                <button type="button" id="searchBtn">Search</button>
            </div>
        </div>

        <button
            type="button"
            class="hero-feature"
            data-buy
            data-name="<?= htmlspecialchars($spotName, ENT_QUOTES) ?>"
            data-price="<?= htmlspecialchars($spotPrice, ENT_QUOTES) ?>"
            data-img="<?= htmlspecialchars($spotCover, ENT_QUOTES) ?>"
            data-kind="game"
            data-wa="<?= htmlspecialchars($spotWa, ENT_QUOTES) ?>"
        >
            <img src="<?= htmlspecialchars($spotWide) ?>" alt="<?= htmlspecialchars($spotName) ?>">
            <div class="hero-feature-copy">
                <small>Featured</small>
                <h2><?= htmlspecialchars($spotName) ?></h2>
                <p><?= htmlspecialchars($spotTag) ?> · <?= htmlspecialchars($spotPrice) ?></p>
                <span class="btn btn-primary">Buy now</span>
            </div>
        </button>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="how-strip">
            <div class="how-card">
                <em>01</em>
                <h3>Choose a title</h3>
                <p>Pick a game, app or eFootball tips pack from the catalog.</p>
            </div>
            <div class="how-card">
                <em>02</em>
                <h3>Pay HaloPesa</h3>
                <p>Send to <strong>0627041240</strong>, then share the screenshot on WhatsApp.</p>
            </div>
            <div class="how-card">
                <em>03</em>
                <h3>Unlock download</h3>
                <p>Admin sends your access code. Enter it and continue.</p>
            </div>
        </div>
    </div>
</section>

<section class="alt drops-rotator" id="hot">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="section-kicker">Trending</div>
                <h2>Tap a cover to buy</h2>
            </div>
            <p>GTA, Subway Surfers, eFootball and more — prices as set in admin.</p>
        </div>

        <div class="cover-rotator">
            <button class="rotator-nav prev" type="button" data-rotator-nav="prev" aria-label="Previous">&lsaquo;</button>
            <?php foreach ($showcase as $i => $drop): ?>
                <article
                    class="cover-slide<?= $i === 0 ? ' active' : ($i === 1 ? ' next' : ($i === count($showcase) - 1 ? ' prev' : '')) ?>"
                    data-buy
                    data-name="<?= htmlspecialchars($drop["name"], ENT_QUOTES) ?>"
                    data-price="Price: Set by Admin"
                    data-img="<?= htmlspecialchars($drop["image"], ENT_QUOTES) ?>"
                    data-kind="game"
                    data-wa="<?= htmlspecialchars(millz_buy_url($drop["name"]), ENT_QUOTES) ?>"
                    data-tag="<?= htmlspecialchars($drop["tag"]) ?>"
                >
                    <img src="<?= htmlspecialchars($drop["image"]) ?>" alt="<?= htmlspecialchars($drop["name"]) ?>">
                </article>
            <?php endforeach; ?>
            <button class="rotator-nav next" type="button" data-rotator-nav="next" aria-label="Next">&rsaquo;</button>
        </div>
        <div class="rotator-caption" id="rotatorCaption">Grand Theft Auto V</div>
    </div>
</section>

<section id="games">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="section-kicker">Catalog</div>
                <h2>Latest games</h2>
            </div>
            <a href="games.php" class="btn btn-outline">All games</a>
        </div>

        <div class="catalog-toolbar" id="search">
            <div class="category-list" id="categories">
                <a href="#games" class="category active" data-filter="all">All</a>
                <?php if (!empty($gameCategories)): ?>
                    <?php foreach ($gameCategories as $category): ?>
                        <a href="#games" class="category" data-filter="<?= htmlspecialchars($category["name"]) ?>">
                            <?= htmlspecialchars($category["name"]) ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a href="#games" class="category" data-filter="Action">Action</a>
                    <a href="#games" class="category" data-filter="Arcade">Arcade</a>
                    <a href="#games" class="category" data-filter="Sports">Sports</a>
                    <a href="#games" class="category" data-filter="Shooter">Shooter</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="products-grid" id="gameGrid">
            <?php foreach ($homeGames as $item): $kind = "game"; require "includes/product-card.php"; endforeach; ?>
        </div>
        <?php if ($catalogNote): ?>
            <p class="empty" style="margin-top:18px;">Published titles from admin replace this catalog automatically. Prices stay as set in admin.</p>
        <?php endif; ?>
    </div>
</section>

<section class="alt" id="apps">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="section-kicker">Software</div>
                <h2>Apps</h2>
            </div>
            <a href="apps.php" class="btn btn-outline">All apps</a>
        </div>

        <div class="category-list">
            <a href="#apps" class="category active" data-app-filter="all">All</a>
            <?php foreach ($appCategories as $category): ?>
                <a href="#apps" class="category" data-app-filter="<?= htmlspecialchars($category["name"]) ?>">
                    <?= htmlspecialchars($category["name"]) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($apps)): ?>
            <div class="products-grid">
                <?php foreach ($apps as $item): $kind = "app"; require "includes/product-card.php"; endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty">Apps published in admin will appear here with Buy now and Get access.</div>
        <?php endif; ?>
    </div>
</section>

<section id="tips">
    <div class="container">
        <div class="tips-banner">
            <img src="assets/img/games/efootball-wide.jpg" alt="eFootball tips">
            <div class="tips-banner-copy">
                <div class="section-kicker">eFootball</div>
                <h2>Tips from MILLZ</h2>
                <p>Skills, dribbling, formations and controls. Buy the pack or watch free tutorials on YouTube.</p>
                <div class="hero-buttons">
                    <button
                        type="button"
                        class="btn btn-primary"
                        data-buy
                        data-name="eFootball Tips Pack"
                        data-price="Price: Set by Admin"
                        data-img="assets/img/games/efootball.jpg"
                        data-kind="tip"
                        data-wa="<?= htmlspecialchars(millz_buy_url('eFootball Tips Pack', null, 'tip'), ENT_QUOTES) ?>"
                    >Buy tips</button>
                    <a href="efootball-tips.php" class="btn btn-outline">View all tips</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="alt">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="section-kicker">Official channels</div>
                <h2>Follow MILLZ</h2>
            </div>
        </div>
        <div class="social-row">
            <?php foreach ($socials as $social): ?>
                <a class="social-pill" href="<?= htmlspecialchars($social["url"]) ?>" target="_blank" rel="noopener">
                    <img src="<?= htmlspecialchars($social["icon"]) ?>" alt="">
                    <div>
                        <strong><?= htmlspecialchars($social["label"]) ?></strong>
                        <span><?= htmlspecialchars($social["handle"]) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="contact">
    <div class="container home-split">
        <div class="home-panel" id="request">
            <div class="section-kicker">Requests</div>
            <h2>Can’t find a title?</h2>
            <p>Send the name and platform. We will add it when it is available.</p>
            <form action="api/request.php" method="POST">
                <div class="form-group">
                    <label>Game name</label>
                    <input type="text" name="game_name" required placeholder="Enter game name">
                </div>
                <div class="form-group">
                    <label>Platform</label>
                    <select name="platform" required>
                        <option value="">Select platform</option>
                        <option value="Android">Android</option>
                        <option value="iPhone / iOS">iPhone / iOS</option>
                        <option value="Windows">Windows</option>
                        <option value="PlayStation">PlayStation</option>
                        <option value="Xbox">Xbox</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" placeholder="0627041240">
                </div>
                <button type="submit" class="btn btn-primary">Send request</button>
            </form>
        </div>

        <div class="home-panel">
            <div class="section-kicker">Support</div>
            <h2>Contact</h2>
            <p>WhatsApp 0627041240 · HaloPesa 0627041240</p>
            <div class="contact-grid" style="grid-template-columns:1fr;margin-bottom:18px;">
                <a class="contact-card" href="https://wa.me/255627041240" target="_blank" rel="noopener">
                    <img src="assets/img/social/whatsapp.svg" alt="WhatsApp" style="width:28px;height:28px;margin:0 auto 8px;">
                    <h3>WhatsApp</h3>
                    <p>Buy, access codes and custom requests.</p>
                </a>
            </div>
            <form action="api/message.php" method="POST">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" required placeholder="How can we help?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send message</button>
            </form>
        </div>
    </div>
</section>

<section class="alt" id="access">
    <div class="container home-split">
        <div class="home-panel">
            <div class="section-kicker">Already paid?</div>
            <h2>Access code</h2>
            <p>Enter your code to continue to the download.</p>
            <a href="access.php" class="btn btn-primary">Verify code</a>
        </div>
        <div class="home-panel">
            <div class="section-kicker">Payments</div>
            <h2>How to pay</h2>
            <p>HaloPesa is the primary number. Other wallets are accepted on request.</p>
            <div class="payment-list">
                <div class="payment"><i class="fa-solid fa-wallet"></i> HaloPesa 0627041240</div>
                <div class="payment"><i class="fa-solid fa-mobile-screen"></i> M-Pesa</div>
                <div class="payment"><i class="fa-solid fa-mobile-screen"></i> Airtel Money</div>
                <div class="payment"><i class="fa-solid fa-mobile-screen"></i> Tigo Pesa</div>
            </div>
        </div>
    </div>
</section>

<?php require "includes/site-footer.php"; ?>

</body>
</html>
