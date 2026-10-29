<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

function isLoggedIn(): bool { return isset($_SESSION['user_id']); }

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    $st = getPDO()->prepare("SELECT id,name,email,phone,address FROM users WHERE id=?");
    $st->execute([$_SESSION['user_id']]);
    return $st->fetch() ?: null;
}

function cartCount(): int {
    if (isLoggedIn()) {
        $st = getPDO()->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart WHERE user_id=?");
        $st->execute([$_SESSION['user_id']]);
        return (int)$st->fetchColumn();
    }
    return array_sum($_SESSION['cart'] ?? []);
}

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }