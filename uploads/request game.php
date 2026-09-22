<?php
require_once 'includes/header.php';
require_once 'config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_title = trim($_POST['game_title'] ?? '');
    $platform   = trim($_POST['platform'] ?? '');
    $email      = trim($_POST['email'] ?? '');

    if (empty($game_title)) {
        $error = "Tafadhali weka jina la mchezo unaoomba.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO game_requests (game_title, platform, email, created_at) VALUES (:title, :platform, :email, NOW())");
            $stmt->execute([
                'title'    => $game_title,
                'platform' => $platform,
                'email'    => $email
            ]);
            $message = "Ombi lako la mchezo limepokelewa kikamilifu!";
        } catch (PDOException $e) {
            $error = "Hitilafu imetokea. Jaribu tena.";
        }
    }
}
?>

<div style="max-width: 500px; margin: 2rem auto; background: #1e293b; padding: 2rem; border-radius: 8px;">
    <h2 style="color: #38bdf8; margin-bottom: 1rem; text-align: center;">Omba Mchezo (Request Game)</h2>

    <?php if ($message): ?>
        <p style="color: #4ade80; margin-bottom: 1rem;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="color: #f87171; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Jina la Game *</label>
            <input type="text" name="game_title" required style="width: 100%; padding: 0.75rem; border-radius: 4px; border: 1px solid #334155; background: #0f172a; color: white;">
        </div>
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Platform (PC, PS4, Android...)</label>
            <input type="text" name="platform" style="width: 100%; padding: 0.75rem; border-radius: 4px; border: 1px solid #334155; background: #0f172a; color: white;">
        </div>
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Email Yako (Optional)</label>
            <input type="email" name="email" style="width: 100%; padding: 0.75rem; border-radius: 4px; border: 1px solid #334155; background: #0f172a; color: white;">
        </div>
        <button type="submit" style="width: 100%; padding: 0.75rem; background: #0284c7; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Tuma Ombi</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>