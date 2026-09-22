<?php
require_once "config/database.php";

$message_sent = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($message === "") {
        $error = "Please enter your message.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO messages
                (name, phone, email, subject, message, status)
                VALUES
                (:name, :phone, :email, :subject, :message, 'unread')
            ");
            $stmt->execute([
                ":name" => $name,
                ":phone" => $phone,
                ":email" => $email,
                ":subject" => $subject,
                ":message" => $message
            ]);
            $message_sent = true;
        } catch (PDOException $e) {
            $error = "Imeshindikana kutuma ujumbe.";
        }
    }
}

$pageTitle = "Contact | MILLZ GAMES";
$current = "contact";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require "includes/site-head.php"; ?>
<style>
.contact-page-grid { display:grid; grid-template-columns:.9fr 1.1fr; gap:22px; }
.info { margin-bottom:20px; }
.info strong { display:block; margin-bottom:4px; }
.info span { color:#8f9c97; }
@media (max-width:800px){ .contact-page-grid { grid-template-columns:1fr; } }
</style>
</head>
<body>
<?php require "includes/site-header.php"; ?>

<section class="page-hero" style="--page-image:url('assets/img/banner-setup.jpg');">
    <div class="container">
        <div class="section-kicker">Support</div>
        <h1>Contact <span>us</span></h1>
        <p>Questions about a game, app, access code or download.</p>
    </div>
</section>

<main class="page-wrap">
    <div class="contact-page-grid">
        <div class="page-card">
            <div class="section-kicker">Direct</div>
            <h2 style="font-family:Syne,sans-serif;font-size:32px;letter-spacing:-.03em;margin:6px 0 18px;">Get in touch</h2>

            <div class="info">
                <strong>WhatsApp</strong>
                <span>0683179360</span><br>
                <a class="btn btn-primary" href="https://wa.me/255683179360" target="_blank" rel="noopener" style="margin-top:10px;">
                    <?= millz_social_mark("whatsapp") ?> WhatsApp Us
                </a>
            </div>
            <div class="info">
                <strong>Phone</strong>
                <span>0627041240</span>
            </div>
            <div class="info">
                <strong>YouTube</strong>
                <span>@millzjasper</span>
            </div>
            <div class="info">
                <strong>Payments</strong>
                <span>HaloPesa: 0627041240</span>
            </div>
        </div>

        <div class="page-card">
            <h2 style="font-family:Syne,sans-serif;font-size:22px;letter-spacing:-.03em;margin-bottom:16px;">Send a message</h2>

            <?php if ($message_sent): ?>
                <div class="success">Ujumbe wako umetumwa successfully.</div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" maxlength="150" placeholder="Your name">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" maxlength="30" placeholder="Phone number">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" maxlength="150" placeholder="Email address">
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" maxlength="200" placeholder="Subject">
                    </div>
                    <div class="form-group full">
                        <label>Message</label>
                        <textarea name="message" required placeholder="Write your message here..."></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> SEND MESSAGE
                </button>
            </form>
        </div>
    </div>
</main>

<?php require "includes/site-footer.php"; ?>
</body>
</html>
