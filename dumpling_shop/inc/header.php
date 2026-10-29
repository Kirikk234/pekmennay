<?php 
require_once __DIR__ . '/functions.php'; 
$user = currentUser(); 
// Эта строка автоматически определяет путь к вашей папке
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Пельменная «У Петровича»' ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/style.css?v=<?= time() ?>">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a href="<?= $baseUrl ?>/index.php" class="logo">🥟 Пельменная</a>
    <button class="burger" id="burger" aria-label="Меню">☰</button>
    <nav class="nav" id="nav">
      <a href="<?= $baseUrl ?>/index.php">Главная</a>
      <a href="<?= $baseUrl ?>/index.php#menu">Меню</a>
      <a href="<?= $baseUrl ?>/cart.php">Корзина <span class="badge" id="cart-count"><?= cartCount() ?></span></a>
      <?php if ($user): ?>
        <a href="<?= $baseUrl ?>/profile.php">👤 <?= e($user['name']) ?></a>
        <a href="<?= $baseUrl ?>/logout.php">Выйти</a>
      <?php else: ?>
        <a href="<?= $baseUrl ?>/auth.php">Вход</a>
        <a href="<?= $baseUrl ?>/auth.php?mode=register" class="btn btn-primary">Регистрация</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main>