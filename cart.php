<?php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_qty'])) {
    $id=(int)$_POST['product_id']; $qty=(int)$_POST['qty'];
    if ($qty>0) $_SESSION['cart'][$id]['qty']=$qty; else cartRemove($id);
    redirect('cart.php');
}
if (isset($_GET['remove'])) { cartRemove((int)$_GET['remove']); redirect('cart.php'); }
$items = cartItems(); $pageTitle = 'Корзина'; $currentPage = 'cart.php';
require_once 'includes/header.php';
?>

<div class="breadcrumb">
  <a href="index.php">Главная</a><span class="breadcrumb-sep">/</span><span>Корзина</span>
</div>

<?php if (!$items): ?>
<div class="empty-box">
  <div class="empty-box__icon">🛒</div>
  <div class="empty-box__title">Корзина пуста</div>
  <p class="empty-box__text">Добавьте товары из каталога</p>
  <a href="catalog.php" class="btn btn-primary">Перейти в каталог</a>
</div>
<?php else: ?>

<div class="cart-layout">
  <div>
    <h1 class="cart-title">Корзина</h1>
    <?php foreach ($items as $id => $item): ?>
    <div class="cart-item">
      <div class="cart-item__thumb">
        <?php $r=db()->prepare("SELECT id,image FROM products WHERE id=?"); $r->execute([$id]); $r=$r->fetch();
        if ($r && !empty($r['image'])): ?>
          <img src="image.php?id=<?= (int)$r['id'] ?>" alt="">
        <?php else: ?><?= mb_strtoupper(mb_substr($item['name'],0,1)) ?><?php endif; ?>
      </div>
      <div>
        <div class="cart-item__name"><?= h($item['name']) ?></div>
        <div class="cart-item__price"><?= price($item['price']) ?> / шт.</div>
      </div>
      <div class="cart-item__right">
        <div class="cart-item__total"><?= price($item['price'] * $item['qty']) ?></div>
        <form method="post">
          <input type="hidden" name="product_id" value="<?= $id ?>">
          <input type="hidden" name="qty" id="qty_<?= $id ?>" value="<?= (int)$item['qty'] ?>">
          <div class="cart-qty-ctrl">
            <button type="button" class="cart-qty-btn" onclick="cqty(<?= $id ?>,-1)">−</button>
            <span class="cart-qty-num" id="disp_<?= $id ?>"><?= (int)$item['qty'] ?></span>
            <button type="button" class="cart-qty-btn" onclick="cqty(<?= $id ?>,+1)">+</button>
          </div>
          <button type="submit" name="update_qty" id="sub_<?= $id ?>" style="display:none"></button>
        </form>
        <a href="cart.php?remove=<?= $id ?>" class="btn btn-danger btn-sm">Удалить</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="cart-aside">
    <div class="summary-box">
      <div class="summary-box__title">Итого</div>
      <?php $del = cartTotal() >= 50000 ? 0 : 2500; ?>
      <div class="summary-row">
        <span class="summary-muted">Товары (<?= cartCount() ?> шт.)</span>
        <span class="summary-mono"><?= price(cartTotal()) ?></span>
      </div>
      <div class="summary-row">
        <span class="summary-muted">Доставка</span>
        <span class="summary-mono"><?= $del ? price($del) : 'Бесплатно' ?></span>
      </div>
      <div class="summary-row summary-row--total">
        <span>Итого</span>
        <span class="summary-mono"><?= price(cartTotal() + $del) ?></span>
      </div>
      <a href="checkout.php" class="btn btn-primary btn-full btn-lg" style="margin-top:16px">Оформить заказ</a>
      <a href="catalog.php"  class="btn btn-outline  btn-full" style="margin-top:8px">Продолжить покупки</a>
    </div>
  </div>
</div>

<script>
function cqty(id, d) {
  const disp = document.getElementById('disp_'+id);
  const inp  = document.getElementById('qty_'+id);
  let v = parseInt(disp.textContent) + d;
  if (v < 1) v = 1;
  disp.textContent = v; inp.value = v;
  clearTimeout(inp._t);
  inp._t = setTimeout(() => document.getElementById('sub_'+id).click(), 700);
}
</script>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>
