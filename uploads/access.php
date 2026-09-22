<?php
require_once 'includes/auth.php';
requireLogin();

require_once 'includes/header.php';
require_once 'config/database.php';

$game_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$game_id) {
    header("Location: index.php");
    exit();
}

// Angalia kama mchezo upo na kama mtumiaji anaruhusiwa kupakua
try {
    $stmt = $pdo->prepare("
        SELECT g.* 
        FROM games g
        INNER JOIN purchases p ON g.id = p.game_id
        WHERE p.user_id = :user_id AND g.id = :game_id AND p.status = 'completed'
    ");
    $stmt->execute(['user_id' => $user_id, 'game_id' => $game_id]);
    $game = $stmt->fetch();

    if (!$game) {
        $accessGranted = false;
    } else {
        $accessGranted = true;
    }
} catch (PDOException $e) {
    $accessGranted = false;
}
?>

<div style="max-width: 600px; margin: 3rem auto; background: #1e293b; padding: 2rem; border-radius: 8px; text-align: center;">
    <?php if ($accessGranted): ?>
        <h2 style="color: #38bdf8; margin-bottom: 1rem;"><?php echo htmlspecialchars($game['title']); ?></h2>
        <p style="margin-bottom: 1.5rem;">Malipo yako yamethibitishwa! Unaweza kupakua mchezo wako sasa.</p>
        <a href="downloads/<?php echo htmlspecialchars($game['file_path']); ?>" style="display: inline-block; padding: 0.75rem 1.5rem; background: #22c55e; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">Download Now</a>
    <?php else: ?>
        <h2 style="color: #ef4444; margin-bottom: 1rem;">Huna Ruhusa</h2>
        <p style="margin-bottom: 1.5rem;">Bado hujalipia mchezo huu au malipo yako yapo kwenye mchakato.</p>
        <a href="index.php" style="display: inline-block; padding: 0.75rem 1.5rem; background: #0284c7; color: white; text-decoration: none; border-radius: 4px;">Rudi Mwanzo</a>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>