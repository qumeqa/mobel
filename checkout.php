<?php
require_once 'includes/config.php';
if (!cartItems()) redirect('cart.php');
$u = currentUser(); $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name=$_POST['name']??''; $email=$_POST['email']??''; $phone=$_POST['phone']??'';
    $addr=$_POST['address']??''; $city=$_POST['city']??''; $postal=$_POST['postal']??'';
    $delivery=$_POST['delivery']??'courier'; $comment=$_POST['comment']??'';
    if (!trim($name))  $errors['name']  = 'Введите имя';
    if (!filter_var(trim($email),FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Некорректный email';
    if ($delivery==='courier' && !trim($addr)) $errors['address'] = 'Введите адрес';
    if (!$errors) {
        $del = cartTotal() >= 50000 ? 0 : 2500; $total = cartTotal() + $del;
        $ins = db()->prepare("INSERT INTO orders(user_id,name,email,phone,address,city,postal,delivery_method,comment,total) VALUES(?,?,?,?,?,?,?,?,?,?)");
        $ins->execute([$u['id']??null,trim($name),trim($email),trim($phone),$delivery==='pickup'?'Самовывоз':trim($addr),trim($city),trim($postal),$delivery,trim($comment),$total]);
        $oid = db()->lastInsertId();
        foreach (cartItems() as $id => $item) {
            db()->prepare("INSERT INTO order_items(order_id,product_id,name,price,quantity) VALUES(?,?,?,?,?)")->execute([$oid,$id,$item['name'],$item['price'],$item['qty']]);
            db()->prepare("UPDATE products SET stock=GREATEST(0,stock-?) WHERE id=?")->execute([$item['qty'],$id]);
        }
        cartClear(); redirect('order_success.php?id='.$oid);
    }
}
$pageTitle = 'Оформление заказа'; $currentPage = 'checkout.php';
require_once 'includes/header.php';
?>
<div class="breadcrumb">
  <a href="index.php">Главная</a><span class="breadcrumb-sep">/</span>
  <a href="cart.php">Корзина</a><span class="breadcrumb-sep">/</span>
  <span>Оформление</span>
</div>
<div class="checkout-layout">
  <div>
    <h1 class="checkout-title">Оформление заказа</h1>
    <form method="post" class="form-stack">
      <div class="form-row">
        <div class="field">
          <label class="field-label">Имя *</label>
          <input class="field-input" type="text" name="name" required value="<?= h($_POST['name']??$u['name']??'') ?>">
          <?php if (isset($errors['name'])): ?><span class="field-error"><?= h($errors['name']) ?></span><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label">Телефон</label>
          <input class="field-input" type="tel" name="phone" value="<?= h($_POST['phone']??$u['phone']??'') ?>">
        </div>
      </div>
      <div class="field">
        <label class="field-label">Email *</label>
        <input class="field-input" type="email" name="email" required value="<?= h($_POST['email']??$u['email']??'') ?>">
        <?php if (isset($errors['email'])): ?><span class="field-error"><?= h($errors['email']) ?></span><?php endif; ?>
      </div>
      <div class="field">
        <label class="field-label">Способ получения</label>
        <div class="radio-cards">
          <input type="radio" id="d_c" name="delivery" value="courier" class="radio-card-input" <?=(($_POST['delivery']??'courier')==='courier')?'checked':''?>>
          <label for="d_c" class="radio-card-label"><span class="icon">🚚</span>Курьер</label>
          <input type="radio" id="d_p" name="delivery" value="pickup"  class="radio-card-input" <?=(($_POST['delivery']??'')==='pickup')?'checked':''?>>
          <label for="d_p" class="radio-card-label"><span class="icon">🏪</span>Самовывоз</label>
        </div>
      </div>
      <div id="addr-block" class="form-stack">
        <div class="form-row">
          <div class="field"><label class="field-label">Город</label><input class="field-input" type="text" name="city" value="<?= h($_POST['city']??'') ?>"></div>
          <div class="field"><label class="field-label">Индекс</label><input class="field-input" type="text" name="postal" value="<?= h($_POST['postal']??'') ?>"></div>
        </div>
        <div class="field">
          <label class="field-label">Адрес доставки *</label>
          <input class="field-input" type="text" name="address" placeholder="Улица, дом, кв." value="<?= h($_POST['address']??'') ?>">
          <?php if (isset($errors['address'])): ?><span class="field-error"><?= h($errors['address']) ?></span><?php endif; ?>
        </div>
      </div>
      <div class="field">
        <label class="field-label">Комментарий</label>
        <textarea class="field-textarea" name="comment" rows="3" placeholder="Пожелания по доставке..."><?= h($_POST['comment']??'') ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-lg btn-full">Подтвердить заказ</button>
    </form>
  </div>
  <div class="checkout-aside">
    <div class="summary-box">
      <div class="summary-box__title">Ваш заказ</div>
      <?php foreach (cartItems() as $id => $item): ?>
      <div class="checkout-order-line">
        <span class="checkout-order-name"><?= h($item['name']) ?></span>
        <span class="checkout-order-qty"><?= (int)$item['qty'] ?> шт.</span>
        <span class="checkout-order-price"><?= price($item['price']*$item['qty']) ?></span>
      </div>
      <?php endforeach; ?>
      <?php $del = cartTotal() >= 50000 ? 0 : 2500; ?>
      <div class="summary-row">
        <span class="summary-muted">Доставка</span>
        <span class="summary-mono"><?= $del ? price($del) : 'Бесплатно' ?></span>
      </div>
      <div class="summary-row summary-row--total">
        <span>Итого</span>
        <span class="summary-mono"><?= price(cartTotal()+$del) ?></span>
      </div>
    </div>
  </div>
</div>
<script>
const radios = document.querySelectorAll('input[name=delivery]');
const block  = document.getElementById('addr-block');
function tog() { block.style.display = document.getElementById('d_p').checked ? 'none' : ''; }
radios.forEach(r => r.addEventListener('change', tog)); tog();
</script>
<?php require_once 'includes/footer.php'; ?>
