<?php
require_once "config/database.php";
require_once "includes/helpers.php";
$pageTitle = "eFootball Tips | MILLZ GAMES";
$current = "tips";
$tipsBuy = millz_buy_url("eFootball Tips Pack", null, "tip");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require "includes/site-head.php"; ?>
<style>
.tips-page-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
.tip-card { background:#0c1112; border:1px solid #1d2828; border-radius:14px; padding:24px; }
.tip-card .icon { font-size:28px; margin-bottom:10px; }
.tip-card h3 { font-family:Syne,sans-serif; font-size:16px; margin-bottom:8px; }
.tip-card p { color:#8f9c97; }
.skill-list { display:flex; flex-wrap:wrap; gap:10px; margin-top:18px; }
@media (max-width:800px){ .tips-page-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:520px){ .tips-page-grid { grid-template-columns:1fr; } }
</style>
</head>
<body>
<?php require "includes/site-header.php"; ?>

<section class="page-hero" style="--page-image:url('assets/img/banner-football.jpg');">
    <div class="container">
        <div class="section-kicker">Coaching</div>
        <h1>eFootball <span>tips</span></h1>
        <p>Skills, dribbling, shooting and controls from MILLZ JASPER.</p>
        <div class="hero-buttons" style="margin-top:22px;">
            <button
                type="button"
                class="btn btn-primary"
                data-buy
                data-name="eFootball Tips Pack"
                data-price="Price: Set by Admin"
                data-img="assets/img/games/efootball.jpg"
                data-kind="tip"
                data-wa="<?= htmlspecialchars($tipsBuy, ENT_QUOTES) ?>"
            >Buy tips</button>
            <a class="btn btn-outline" href="https://youtube.com/@millzjasper" target="_blank" rel="noopener">
                <img src="assets/img/social/youtube.svg" alt="" style="width:18px;height:18px;"> Watch MILLZ JASPER
            </a>
        </div>
    </div>
</section>

<main class="page-wrap">
    <div class="section-title">
        <div>
            <div class="section-kicker">Tutorials</div>
            <h2>Learn the moves</h2>
        </div>
    </div>

    <div class="tips-page-grid">
        <div class="tip-card">
            <div class="icon">⚡</div>
            <h3>Double Touch</h3>
            <p>Learn how to perform Double Touch and use it to beat defenders.</p>
        </div>
        <div class="tip-card">
            <div class="icon">🎮</div>
            <h3>Body Feint</h3>
            <p>Master Body Feint movement and create space against defenders.</p>
        </div>
        <div class="tip-card">
            <div class="icon">🔄</div>
            <h3>Reverse Step Over</h3>
            <p>Timing and movement needed to land Reverse Step Over.</p>
        </div>
        <div class="tip-card">
            <div class="icon">💥</div>
            <h3>Blitz Curler</h3>
            <p>Powerful curling shots and better finishing in the box.</p>
        </div>
        <div class="tip-card">
            <div class="icon">🚀</div>
            <h3>Low Screamer</h3>
            <p>Low driven shots and when to fire them in a match.</p>
        </div>
        <div class="tip-card">
            <div class="icon">⚽</div>
            <h3>Dribbling Tips</h3>
            <p>Close control, movement and 1v1 situations.</p>
        </div>
    </div>

    <div class="page-card" style="margin-top:32px;">
        <div class="section-kicker">More coming</div>
        <h2 style="font-family:Syne,sans-serif;font-size:32px;letter-spacing:-.03em;margin:8px 0 10px;">Full playbook</h2>
        <p style="color:#8b8e8c;">New eFootball tutorials are posted regularly on YouTube.</p>
        <div class="skill-list">
            <span class="tag">Shooting</span>
            <span class="tag">Passing</span>
            <span class="tag">Dribbling</span>
            <span class="tag">Skills</span>
            <span class="tag">Free Kicks</span>
            <span class="tag">Finishing</span>
            <span class="tag">Defending</span>
            <span class="tag">Formations</span>
            <span class="tag">Controls</span>
            <span class="tag">Gameplay Tips</span>
        </div>
    </div>

    <div class="access-box" style="margin-top:36px;">
        <h2>Buy the tips pack</h2>
        <p>Pay via HaloPesa, receive your access code, then follow the tutorials on YouTube.</p>
        <div class="hero-buttons" style="justify-content:center;">
            <button
                type="button"
                class="btn btn-primary"
                data-buy
                data-name="eFootball Tips Pack"
                data-price="Price: Set by Admin"
                data-img="assets/img/games/efootball.jpg"
                data-kind="tip"
                data-wa="<?= htmlspecialchars($tipsBuy, ENT_QUOTES) ?>"
            >Buy on WhatsApp</button>
        </div>
    </div>
</main>

<?php require "includes/site-footer.php"; ?>
</body>
</html>
