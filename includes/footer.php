</main>

<footer class="site-footer">
  <div class="footer-wrap">
    <?php $d = str_contains($_SERVER['PHP_SELF'], '/admin/') ? '../' : ''; ?>
    <div>
      <div class="footer-logo"><?= SITE_NAME ?></div>
      <p class="footer-desc">Мебель для тех, кто ценит форму и функцию. Натуральные материалы, честная цена.</p>
    </div>
    <div>
      <div class="footer-col-title">Каталог</div>
      <?php foreach (db()->query("SELECT name,slug FROM categories ORDER BY id")->fetchAll() as $c): ?>
        <a href="<?= $d ?>catalog.php?cat=<?= h($c['slug']) ?>" class="footer-link"><?= h($c['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <div>
      <div class="footer-col-title">Покупателям</div>
      <a href="<?= $d ?>cart.php"     class="footer-link">Корзина</a>
      <a href="<?= $d ?>account.php"  class="footer-link">Личный кабинет</a>
      <a href="<?= $d ?>checkout.php" class="footer-link">Оформление заказа</a>
    </div>
    <div>
      <div class="footer-col-title">Контакты</div>
      <a href="tel:+74951234567"       class="footer-link">+7 495 123-45-67</a>
      <a href="mailto:hello@moebel.ru" class="footer-link">hello@moebel.ru</a>
      <span class="footer-link">Пн–Пт 9:00–19:00</span>
    </div>
  </div>
  <div class="footer-bottom">
    <span><?= SITE_NAME ?> © <?= date('Y') ?></span>
    <span>Интернет-магазин мебели</span>
  </div>
</footer>

</body>
</html>
