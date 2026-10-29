<?php
$pageTitle = 'Оформление заказа';
require __DIR__ . '/inc/header.php';
if (!isLoggedIn()) { header('Location: /pekmenay/dumpling_shop/auth.php'); exit; }
$user = currentUser();
?>
<section class="container page">
  <h1 class="section-title">Оформление заказа</h1>
  <div style="max-width: 500px; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
    <form class="form" id="checkout-form">
      <label>Адрес доставки
        <textarea name="address" rows="3" required placeholder="Город, улица, дом, квартира"><?= e($user['address'] ?? '') ?></textarea>
      </label>
      <div style="display:flex; justify-content:space-between; font-size:18px; font-weight:bold; margin: 10px 0;">
        <span>Итого:</span>
        <span id="checkout-total">0 ₽</span>
      </div>
      <button type="submit" class="btn btn-primary btn-lg" style="width:100%">Подтвердить заказ</button>
      <p class="msg" id="form-msg"></p>
    </form>
  </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', async () => {
  const form = document.getElementById('checkout-form');
  if (!form) return;

  const res = await fetch('/pekmenay/dumpling_shop/api.php?action=get');
  const data = await res.json();
  document.getElementById('checkout-total').textContent = (data.total || 0).toLocaleString('ru-RU') + ' ₽';

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = form.querySelector('button');
    const msg = document.getElementById('form-msg');
    btn.disabled = true; btn.textContent = 'Оформляем...';

    const fd = new FormData(form);
    fd.append('action', 'checkout');

    const req = await fetch('/pekmenay/dumpling_shop/api.php', { method: 'POST', body: fd });
    const result = await req.json();

    if (result.ok) {
      window.location.href = '/pekmenay/dumpling_shop/profile.php';
    } else {
      msg.textContent = result.error || 'Ошибка при оформлении';
      btn.disabled = false; btn.textContent = 'Подтвердить заказ';
    }
  });
});
</script>
<?php require __DIR__ . '/inc/footer.php'; ?>