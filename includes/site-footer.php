<?php
require_once __DIR__ . "/helpers.php";
$socials = millz_socials();
?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <img src="assets/img/logo-mark.png" alt="MILLZ GAMES">
            <strong>MILLZ GAMES</strong>
            <p>Buy games, apps and eFootball tips. Pay via HaloPesa, then unlock with your access code.</p>
            <div class="footer-socials">
                <?php foreach ($socials as $social): ?>
                    <a class="icon-btn social-brand <?= htmlspecialchars($social["id"]) ?>" href="<?= htmlspecialchars($social["url"]) ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($social["label"]) ?>">
                        <?= millz_social_mark($social["id"]) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <h4>EXPLORE</h4>
            <ul>
                <li><a href="games.php">Games</a></li>
                <li><a href="apps.php">Apps</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="efootball-tips.php">eFootball Tips</a></li>
            </ul>
        </div>

        <div>
            <h4>SUPPORT</h4>
            <ul>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="access.php">Access Code</a></li>
                <li><a href="index.php#request">Request a Game</a></li>
                <li><a href="https://wa.me/255683179360" target="_blank" rel="noopener">WhatsApp 0683179360</a></li>
            </ul>
        </div>

        <div>
            <h4>PAYMENTS</h4>
            <ul>
                <li>HaloPesa 0627041240</li>
                <li>M-Pesa</li>
                <li>Airtel Money</li>
                <li>Tigo Pesa</li>
            </ul>
        </div>
    </div>

    <div class="container copyright">
        © <?= date("Y") ?> MILLZ GAMES. All rights reserved. Official site of MILLZ JASPER.
    </div>
</footer>

<div class="buy-modal" id="buyModal" aria-hidden="true">
    <div class="buy-card">
        <button class="buy-close" type="button" data-close-buy aria-label="Close">&times;</button>
        <div class="section-kicker" style="text-align:center;">Complete purchase</div>
        <div class="buy-cover" id="buyCover"></div>
        <h3 id="buyName" style="text-align:center;">Game</h3>
        <div class="price" id="buyPrice" style="text-align:center;">Bei: WhatsApp</div>
        <div class="buy-steps">
            1. Lipa HaloPesa <strong>0627041240</strong><br>
            2. Tuma screenshot kwenye WhatsApp <strong>0683179360</strong><br>
            3. Pokea access code kutoka admin<br>
            4. Unlock download kwenye Access
        </div>
        <div class="buy-actions">
            <a id="buyWhatsApp" class="btn btn-primary" href="https://wa.me/255683179360" target="_blank" rel="noopener">
                <?= millz_social_mark("whatsapp") ?> Continue on WhatsApp
            </a>
        </div>
    </div>
</div>

<script src="/assets/js/site.js"></script>
