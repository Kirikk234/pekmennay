<?php
$pageTitle = 'Личный кабинет';
require __DIR__ . '/inc/header.php';
if (!isLoggedIn()) { header("Location: $baseUrl/auth.php"); exit; }
$user = currentUser();

// Обработка формы редактирования
$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if ($name) {
        $st = getPDO()->prepare("UPDATE users SET name=?, phone=?, address=? WHERE id=?");
        $st->execute([$name, $phone, $address, $user['id']]);
        $successMsg = 'Данные успешно обновлены!';
        // Обновляем данные в переменной для отображения
        $user['name'] = $name;
        $user['phone'] = $phone;
        $user['address'] = $address;
    }
}

$st = getPDO()->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$st->execute([$user['id']]);
$orders = $st->fetchAll();
?>
<section class="container page">
  <div class="profile-header">
    <div class="avatar">👤</div>
    <div class="profile-info">
      <h1>Здравствуйте, <?= e($user['name']) ?>!</h1>
      <p>Email: <?= e($user['email']) ?></p>
    </div>
    <a href="<?= $baseUrl ?>/logout.php" class="btn btn-outline" style="margin-left:auto;">Выйти</a>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
    
    <!-- Блок редактирования -->
    <div style="background:#fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
      <h2 class="section-title" style="margin-bottom: 20px; font-size: 24px;">Редактировать профиль</h2>
      
      <?php if ($successMsg): ?>
        <div style="background: #e8f5e9; color: #388e3c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
          <?= $successMsg ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="form">
        <input type="hidden" name="update_profile" value="1">
        <label>Ваше имя
          <input type="text" name="name" value="<?= e($user['name']) ?>" required>
        </label>
        <label>Телефон
         <input type="tel" name="phone" value="<?= e($user['phone'] ?: '') ?>" placeholder="+375 (29) 123-45-67">
        </label>
        <label>Адрес доставки по умолчанию
          <textarea name="address" rows="3" placeholder="Город, улица, дом, квартира"><?= e($user['address'] ?: '') ?></textarea>
        </label>
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
      </form>
    </div>

    <!-- Блок с контактами (для красоты) -->
    <div style="background:#fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
        <h2 class="section-title" style="margin-bottom: 20px; font-size: 24px;">Мои контакты</h2>
        <p style="margin-bottom: 10px;"><strong>Телефон:</strong> <?= e($user['phone'] ?: 'Не указан') ?></p>
        <p><strong>Адрес по умолчанию:</strong><br> <?= e($user['address'] ?: 'Не указан') ?></p>
    </div>

  </div>

  <h2 class="section-title">История заказов</h2>
  
  <?php if (!$orders): ?>
    <div style="background:#fff;padding:40px;text-align:center;border-radius:16px;">
      <div style="font-size:40px;margin-bottom:16px;">🍽️</div>
      <p style="color:#7a746e;margin-bottom:20px;">Вы пока ничего не заказывали.</p>
      <a href="<?= $baseUrl ?>/index.php#menu" class="btn btn-primary">Перейти в меню</a>
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
            <td><strong><?= number_format($o['total'], 2, '.', ' ') ?> BYN</strong></td>
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