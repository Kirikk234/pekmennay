<?php
$pageTitle = 'Корзина — Пельменная';
require __DIR__ . '/inc/header.php';
?>
<section class="container page">
  <h1 class="section-title">Ваша корзина</h1>
  <div class="cart-grid">
    <div id="cart-items" class="cart-list"></div>
    
    <aside id="cart-sidebar" class="cart-sidebar">
      <h3>Итого</h3>
      <div class="summary-row">
        <span>Товары</span>
        <span id="cart-subtotal">0 BYN</span>
      </div>
      <div class="summary-row">
        <span>Доставка</span>
        <span>Бесплатно</span>
      </div>
      <div class="summary-total">
        <span>К оплате</span>
        <span id="cart-total">0 BYN</span>
      </div>
      <a href="/pekmenay/dumpling_shop/checkout.php" class="btn btn-primary btn-lg" style="width:100%">Оформить заказ</a>
    </aside>
  </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>