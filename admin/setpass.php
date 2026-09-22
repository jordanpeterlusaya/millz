<?php
require_once "../config/database.php";

$username = "Millz";
$password = "Millz 005";
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
    // Futa na uingize upya ili kuondoa makosa ya nafasi (spaces) au hash za zamani
    $pdo->exec("DELETE FROM users WHERE username = 'Millz'");
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute([
        'username' => $username,
        'password' => $hashed_password
    ]);

    echo "<h2 style='color:green;'>Tayari! Username: Millz | Password: Millz 005</h2>";
    echo "<a href='login.php'>Nenda Kwenye Login</a>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>