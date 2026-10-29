</main>
<footer class="site-footer">
  <div class="container">
    <p>© <?= date('Y') ?> Пельменная «У Петровича». Лепим с душой.</p>
  </div>
</footer>
<script>
  // Передаем правильный путь в JS
  const BASE_URL = "<?= $baseUrl ?>";
</script>
<script src="<?= $baseUrl ?>/assets/app.js?v=<?= time() ?>"></script>
</body>
</html>