<?php /* Ожидает $p с полями товара */ ?>
<div class="product-card">
  <a href="product.php?slug=<?= h($p['slug']) ?>" class="product-card__img">
    <?php if ($p['is_new']): ?>
      <span class="product-card__badge"><span class="badge-new">Новинка</span></span>
    <?php elseif (!empty($p['old_price'])): ?>
      <span class="product-card__badge"><span class="badge-sale">Скидка</span></span>
    <?php endif; ?>
    <?php if (!empty($p['image'])): ?>
      <img src="image.php?id=<?= (int)$p['id'] ?>" alt="<?= h($p['name']) ?>" loading="lazy">
    <?php else: ?>
      <div class="product-card__placeholder"><?= mb_strtoupper(mb_substr($p['name'], 0, 1)) ?></div>
    <?php endif; ?>
  </a>
  <div class="product-card__body">
    <div class="product-card__brand"><?= h($p['cat_name'] ?? '') ?></div>
    <a href="product.php?slug=<?= h($p['slug']) ?>" class="product-card__name"><?= h($p['name']) ?></a>
    <div class="product-card__price">
      <span class="price-now"><?= price((float)$p['price']) ?></span>
      <?php if (!empty($p['old_price'])): ?>
        <span class="price-was"><?= price((float)$p['old_price']) ?></span>
      <?php endif; ?>
    </div>
  </div>
  <div class="product-card__footer">
    <form method="post" action="<?= $currentPage ?? 'index.php' ?>">
      <input type="hidden" name="product_id"    value="<?= (int)$p['id'] ?>">
      <input type="hidden" name="product_name"  value="<?= h($p['name']) ?>">
      <input type="hidden" name="product_price" value="<?= (float)$p['price'] ?>">
      <button type="submit" name="add_to_cart" class="btn-add-cart">В корзину</button>
    </form>
  </div>
</div>
