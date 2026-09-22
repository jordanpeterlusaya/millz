<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inakagua kama mtumiaji wa kawaida ameshalogin
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Inakagua kama Admin ameshalogin
function requireAdmin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../admin/login.php");
        exit();
    }
}
?>