<?php
require_once 'includes/config.php';
$pageTitle = 'Главная';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    cartAdd((int)$_POST['product_id'], $_POST['product_name'], (float)$_POST['product_price']);
    $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Товар добавлен в корзину'];
    redirect('index.php');
}
$featured = db()->query("SELECT p.*,c.name AS cat_name,c.slug AS cat_slug FROM products p JOIN categories c ON c.id=p.category_id WHERE p.is_featured=1 ORDER BY p.id DESC LIMIT 6")->fetchAll();
$newArrs  = db()->query("SELECT p.*,c.name AS cat_name,c.slug AS cat_slug FROM products p JOIN categories c ON c.id=p.category_id WHERE p.is_new=1 ORDER BY p.id DESC LIMIT 3")->fetchAll();
$allCats  = db()->query("SELECT c.*,COUNT(p.id) AS cnt FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id ORDER BY cnt DESC")->fetchAll();
$currentPage = 'index.php';
require_once 'includes/header.php';
?>

<section class="hero">
  <div class="hero-body">
    <div class="hero-label">✦ Новая коллекция 2026</div>
    <h1 class="hero-title">
      Мебель, которая<br>остаётся с вами
    </h1>
    <p class="hero-sub">Продуманный дизайн, натуральные материалы и честные цены. Для тех, кто хочет чтобы дом был уютным.</p>
    <div class="hero-actions">
      <a href="catalog.php" class="btn btn-primary btn-lg">Смотреть каталог</a>
      <a href="catalog.php?new=1" class="btn btn-outline btn-lg">Новинки</a>
    </div>
    <!-- <div class="hero-stats">
      <div>
        <div class="hero-stat__num">20+</div>
        <div class="hero-stat__label">Товаров в наличии</div>
      </div>
      <div>
        <div class="hero-stat__num">4</div>
        <div class="hero-stat__label">Ведущих бренда</div>
      </div>
      <div>
        <div class="hero-stat__num">3 года</div>
        <div class="hero-stat__label">Гарантия на всё</div>
      </div>
    </div> -->
  </div>
  <div class="hero-aside">
    <div class="hero-aside-card">
      <!-- <span class="hero-aside-card__icon">🚚</span> -->
      <div>
        <div class="hero-aside-card__title">Бесплатная доставка</div>
        <div class="hero-aside-card__text">При заказе от 50 000 ₽</div>
      </div>
    </div>
    <div class="hero-aside-card">
      <!-- <span class="hero-aside-card__icon">🛡️</span> -->
      <div>
        <div class="hero-aside-card__title">Гарантия 3 года</div>
        <div class="hero-aside-card__text">На все изделия без исключений</div>
      </div>
    </div>
    <div class="hero-aside-card">
      <!-- <span class="hero-aside-card__icon">🪵</span> -->
      <div>
        <div class="hero-aside-card__title">Натуральные материалы</div>
        <div class="hero-aside-card__text">Дуб, берёза, лён, шерсть</div>
      </div>
    </div>
    <div class="hero-aside-card">
      <!-- <span class="hero-aside-card__icon">↩️</span> -->
      <div>
        <div class="hero-aside-card__title">Возврат 30 дней</div>
        <div class="hero-aside-card__text">Без вопросов и лишней волокиты</div>
      </div>
    </div>
  </div>
</section>

<?php if ($featured): ?>
<div class="section-title">
  <h2>Рекомендуем</h2>
  <a href="catalog.php">Весь каталог →</a>
</div>
<div class="products-grid">
  <?php foreach ($featured as $p): include 'includes/product_card.php'; endforeach; ?>
</div>
<?php endif; ?>

<?php if ($newArrs): ?>
<div class="section-title">
  <h2>Новинки</h2>
  <a href="catalog.php?new=1">Все новинки →</a>
</div>
<div class="products-grid">
  <?php foreach ($newArrs as $p): include 'includes/product_card.php'; endforeach; ?>
</div>
<?php endif; ?>

<div class="section-title"><h2>Категории</h2></div>
<div class="cat-grid">
  <?php foreach ($allCats as $i => $c): ?>
  <a href="catalog.php?cat=<?= h($c['slug']) ?>"
     class="cat-card <?= $i === 0 ? 'cat-card--featured' : '' ?>">
    <div class="cat-card__name"><?= h($c['name']) ?></div>
    <div class="cat-card__count"><?= $c['cnt'] ?> товаров</div>
  </a>
  <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
