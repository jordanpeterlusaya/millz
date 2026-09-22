<?php
require_once __DIR__ . "/helpers.php";
$current = $current ?? "";
$socials = millz_socials();
$headerSocials = array_filter($socials, function ($s) {
    return in_array($s["id"], ["instagram", "tiktok", "whatsapp", "youtube"], true);
});
?>
<div class="topbar">
    <div class="topbar-track">
        <span>Official store</span>
        <span>Games · Apps · eFootball</span>
        <span>HaloPesa 0627041240</span>
        <span>Buy · Unlock with access code</span>
        <span>Instagram @young_millz05</span>
        <span>Official store</span>
        <span>Games · Apps · eFootball</span>
        <span>HaloPesa 0627041240</span>
        <span>Buy · Unlock with access code</span>
        <span>Instagram @young_millz05</span>
    </div>
</div>

<header class="site-header">
    <div class="container navbar">
        <a href="index.php" class="brand">
            <img src="assets/img/logo-mark.png" alt="MILLZ GAMES">
            <span class="brand-text">MILLZ <em>GAMES</em></span>
        </a>

        <ul class="nav-links" id="navLinks">
            <li><a href="index.php" class="<?= $current === 'home' ? 'active' : '' ?>">Home</a></li>
            <li><a href="games.php" class="<?= $current === 'games' ? 'active' : '' ?>">Games</a></li>
            <li><a href="apps.php" class="<?= $current === 'apps' ? 'active' : '' ?>">Apps</a></li>
            <li><a href="efootball-tips.php" class="<?= $current === 'tips' ? 'active' : '' ?>">eFootball</a></li>
            <li><a href="contact.php" class="<?= $current === 'contact' ? 'active' : '' ?>">Contact</a></li>
            <li><a href="access.php" class="<?= $current === 'access' ? 'active' : '' ?>">Access</a></li>
            <li><a href="/admin.html">Admin</a></li>
            <li class="header-socials">
                <?php foreach ($headerSocials as $social): ?>
                    <a class="social-brand <?= htmlspecialchars($social["id"]) ?>" href="<?= htmlspecialchars($social["url"]) ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($social["label"]) ?>">
                        <?= millz_social_mark($social["id"]) ?>
                    </a>
                <?php endforeach; ?>
            </li>
        </ul>

        <div class="nav-actions">
            <div class="social-mini">
                <?php foreach ($headerSocials as $social): ?>
                    <a class="social-brand <?= htmlspecialchars($social["id"]) ?>" href="<?= htmlspecialchars($social["url"]) ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($social["label"]) ?>">
                        <?= millz_social_mark($social["id"]) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <a href="games.php" class="btn btn-primary">
                Shop games
            </a>
        </div>

        <button class="menu-btn" id="menuBtn" type="button" aria-label="Open menu" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</header>
