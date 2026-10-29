<?php
$pageTitle = 'Личный кабинет';
require __DIR__ . '/inc/header.php';
if (!isLoggedIn()) { header('Location: /pekmenay/dumpling_shop/auth.php'); exit; }
$user = currentUser();

$st = getPDO()->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$st->execute([$user['id']]);
$orders = $st->fetchAll();
?>
<section class="container page">
  <div class="profile-header">
    <div class="avatar">👤</div>
    <div class="profile-info">
      <h1>Здравствуйте, <?= e($user['name']) ?>!</h1>
      <p>Email: <?= e($user['email']) ?> &nbsp;|&nbsp; Телефон: <?= e($user['phone'] ?: '—') ?></p>
    </div>
    <a href="/pekmenay/dumpling_shop/logout.php" class="btn btn-outline" style="margin-left:auto;">Выйти</a>
  </div>

  <h2 class="section-title">История заказов</h2>
  
  <?php if (!$orders): ?>
    <div style="background:#fff;padding:40px;text-align:center;border-radius:16px;">
      <div style="font-size:40px;margin-bottom:16px;">🍽️</div>
      <p style="color:#7a746e;margin-bottom:20px;">Вы пока ничего не заказывали.</p>
      <a href="/pekmenay/dumpling_shop/index.php#menu" class="btn btn-primary">Перейти в меню</a>
    </div>
  <?php else: ?>
    <table class="orders-table">
      <thead>
        <tr>
          <th>Заказ</th>
          <th>Дата</th>
          <th>Сумма</th>
          <th>Статус</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><strong>#<?= $o['id'] ?></strong></td>
            <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
            <td><strong><?= number_format($o['total'], 0, '.', ' ') ?> ₽</strong></td>
            <td>
              <?php 
                $statusClass = 'status-' . $o['status'];
                $statusName = [
                  'new' => 'Новый',
                  'cooking' => 'Готовится',
                  'delivered' => 'Доставлен',
                  'cancelled' => 'Отменён'
                ][$o['status']] ?? $o['status'];
              ?>
              <span class="status <?= $statusClass ?>"><?= $statusName ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>