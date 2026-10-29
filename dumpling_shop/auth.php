<?php
$mode = ($_GET['mode'] ?? '') === 'register' ? 'register' : 'login';
$pageTitle = ($mode === 'register' ? 'Регистрация' : 'Вход') . ' — Пельменная';
require __DIR__ . '/inc/header.php';
if (isLoggedIn()) { header('Location: /pekmenay/dumpling_shop/profile.php'); exit; }
?>
<div class="container">
  <div class="auth-wrap">
    <div class="auth-tabs">
      <a href="?mode=login" class="<?= $mode==='login'?'active':'' ?>">Вход</a>
      <a href="?mode=register" class="<?= $mode==='register'?'active':'' ?>">Регистрация</a>
    </div>

    <form class="form" id="auth-form">
      <input type="hidden" name="action" value="<?= $mode ?>">
      <?php if ($mode === 'register'): ?>
        <label>Ваше имя <input type="text" name="name" placeholder="Иван" required></label>
        <label>Телефон <input type="tel" name="phone" placeholder="+7 (999) 123-45-67"></label>
      <?php endif; ?>
      <label>Email <input type="email" name="email" placeholder="ivan@example.ru" required></label>
      <label>Пароль <input type="password" name="password" placeholder="Минимум 6 символов" required></label>
      
      <button type="submit" class="btn btn-primary" style="width:100%;margin-top:10px;">
        <?= $mode==='register' ? 'Создать аккаунт' : 'Войти' ?>
      </button>
      <p class="msg" id="form-msg"></p>
    </form>
  </div>
</div>
<?php require __DIR__ . '/inc/footer.php'; ?>