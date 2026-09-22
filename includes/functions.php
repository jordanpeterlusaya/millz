<?php
// Kusafisha text kuzuia mashambulizi ya XSS
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Kuweka bei kwenye format ya TZS
function formatMoney($amount) {
    if ($amount == 0 || $amount === null) {
        return 'Bure';
    }
    return 'TZS ' . number_format($amount, 0, '.', ',');
}

// Kupunguza urefu wa maelezo ya mchezo kwenye cards
function limitText($text, $limit = 90) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}
?>