<?php
$pageTitle = 'Пельменная «У Петровича» — Главная';
require __DIR__ . '/inc/header.php';
$products = getPDO()->query("SELECT * FROM products WHERE is_available=1")->fetchAll();
$cats = array_unique(array_column($products, 'category'));
?>
<section class="hero">
  <div class="container">
    <h1>Настоящие пельмени<br>как у бабушки</h1>
    <p>Лепим вручную каждый день из фермерского мяса. Доставим горячими за 40 минут.</p>
    <a href="#menu" class="btn btn-primary btn-lg">Смотреть меню 🥟</a>
  </div>
</section>

<section class="container page" id="menu">
  <h2 class="section-title">Наше меню</h2>
  
  <div class="filters" id="filters">
    <button class="chip active" data-cat="all">Все</button>
    <?php foreach ($cats as $c): ?>
      <button class="chip" data-cat="<?= e($c) ?>"><?= e($c) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="grid-products" id="grid">
    <?php foreach ($products as $p): ?>
      <article class="card" data-cat="<?= e($p['category']) ?>">
        <div class="card-img">🥟</div>
        <h3><?= e($p['name']) ?></h3>
        <p class="desc"><?= e($p['description']) ?></p>
        <div class="card-bottom">
          <span class="price"><?= number_format($p['price'], 2, '.', ' ') ?> BYN</span>
          <span class="weight"><?= (int)$p['weight'] ?> г</span>
        </div>
        <button class="btn btn-primary add-to-cart" data-id="<?= (int)$p['id'] ?>">В корзину</button>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>