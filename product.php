<?php
require_once 'includes/config.php';
$slug = $_GET['slug'] ?? ''; if (!$slug) redirect('catalog.php');
$s = db()->prepare("SELECT p.*,c.name AS cat_name,c.slug AS cat_slug,b.name AS brand_name FROM products p JOIN categories c ON c.id=p.category_id JOIN brands b ON b.id=p.brand_id WHERE p.slug=?");
$s->execute([$slug]); $p = $s->fetch(); if (!$p) redirect('catalog.php');
$rs = db()->prepare("SELECT p.*,c.name AS cat_name,c.slug AS cat_slug FROM products p JOIN categories c ON c.id=p.category_id WHERE p.category_id=? AND p.id!=? ORDER BY RAND() LIMIT 4");
$rs->execute([$p['category_id'], $p['id']]); $related = $rs->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    cartAdd($p['id'], $p['name'], (float)$p['price'], max(1,(int)($_POST['qty']??1)));
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Добавлено в корзину'];
    redirect('product.php?slug='.urlencode($slug));
}
$specs = json_decode($p['specs'] ?? '{}', true) ?: [];
$pageTitle = $p['name']; $currentPage = 'product.php';
require_once 'includes/header.php';
?>

<div class="breadcrumb">
  <a href="index.php">Главная</a><span class="breadcrumb-sep">/</span>
  <a href="catalog.php?cat=<?= h($p['cat_slug']) ?>"><?= h($p['cat_name']) ?></a><span class="breadcrumb-sep">/</span>
  <span><?= h($p['name']) ?></span>
</div>

<div class="product-layout">
  <div class="product-gallery">
    <div class="product-img">
      <?php if (!empty($p['image'])): ?>
        <img src="image.php?id=<?= (int)$p['id'] ?>" alt="<?= h($p['name']) ?>">
      <?php else: ?>
        <?= mb_strtoupper(mb_substr($p['name'],0,1)) ?>
      <?php endif; ?>
    </div>
    <div class="product-sku">Арт. MB-<?= str_pad($p['id'],4,'0',STR_PAD_LEFT) ?></div>
  </div>

  <div class="product-info">
    <div class="product-brand"><?= h($p['brand_name']) ?></div>
    <h1 class="product-name"><?= h($p['name']) ?></h1>

    <div class="product-prices">
      <span class="product-price"><?= price((float)$p['price']) ?></span>
      <?php if ($p['old_price']): ?>
        <span class="product-price-old"><?= price((float)$p['old_price']) ?></span>
      <?php endif; ?>
    </div>

    <?php if ($p['stock'] > 0): ?>
      <span class="product-stock product-stock--in">В наличии: <?= (int)$p['stock'] ?> шт.</span>
    <?php else: ?>
      <span class="product-stock product-stock--out">Нет в наличии</span>
    <?php endif; ?>

    <?php if ($p['description']): ?>
      <p class="product-desc"><?= nl2br(h($p['description'])) ?></p>
    <?php endif; ?>

    <?php if ($specs): ?>
    <div class="specs">
      <?php foreach ($specs as $k => $v): ?>
      <div class="spec-row">
        <div class="spec-key"><?= h($k) ?></div>
        <div class="spec-val"><?= h($v) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($p['stock'] > 0): ?>
    <form method="post">
      <div class="atc-form">
        <div class="qty-wrap atc-qty">
          <button type="button" class="qty-btn" onclick="const i=this.parentNode.querySelector('.qty-input');if(+i.value>1)i.value=+i.value-1">−</button>
          <input class="qty-input" type="number" name="qty" value="1" min="1" max="<?= (int)$p['stock'] ?>">
          <button type="button" class="qty-btn" onclick="const i=this.parentNode.querySelector('.qty-input');i.value=+i.value+1">+</button>
        </div>
        <button type="submit" name="add_to_cart" class="atc-btn">Добавить в корзину</button>
      </div>
    </form>
    <?php endif; ?>
  </div>
</div>

<?php if ($related): ?>
<div class="section-title">
  <h2>Похожие товары</h2>
  <a href="catalog.php?cat=<?= h($p['cat_slug']) ?>">Смотреть всё →</a>
</div>
<div class="products-grid products-grid--3" style="padding-bottom:64px">
  <?php foreach ($related as $p): include 'includes/product_card.php'; endforeach;
  for ($i = count($related); $i < 3; $i++): ?><div></div><?php endfor; ?>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
