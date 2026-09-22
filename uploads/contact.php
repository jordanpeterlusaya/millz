<?php
require_once 'includes/header.php';
require_once 'config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Tafadhali jaza nafasi zote.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())");
            $stmt->execute([
                'name'    => $name,
                'email'   => $email,
                'message' => $message
            ]);
            $success = "Ujumbe wako umetumwa kikamilifu!";
        } catch (PDOException $e) {
            $error = "Hitilafu imetokea, jaribu tena.";
        }
    }
}
?>

<div style="max-width: 600px; margin: 2rem auto; background: #1e293b; padding: 2rem; border-radius: 8px;">
    <h2 style="color: #38bdf8; margin-bottom: 1rem; text-align: center;">Wasiliana Nasi</h2>

    <?php if ($success): ?>
        <p style="color: #4ade80; margin-bottom: 1rem;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="color: #f87171; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Jina Lako *</label>
            <input type="text" name="name" required style="width: 100%; padding: 0.75rem; border-radius: 4px; border: 1px solid #334155; background: #0f172a; color: white;">
        </div>
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Email Yako *</label>
            <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border-radius: 4px; border: 1px solid #334155; background: #0f172a; color: white;">
        </div>
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Ujumbe *</label>
            <textarea name="message" rows="5" required style="width: 100%; padding: 0.75rem; border-radius: 4px; border: 1px solid #334155; background: #0f172a; color: white;"></textarea>
        </div>
        <button type="submit" style="width: 100%; padding: 0.75rem; background: #0284c7; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Tuma Ujumbe</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>