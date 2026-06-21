<?php
require_once 'includes/config.php';
$s=db()->prepare("SELECT * FROM orders WHERE id=?"); $s->execute([(int)($_GET['id']??0)]); $order=$s->fetch();
if (!$order) redirect('index.php');
$pageTitle='Заказ оформлен'; $currentPage='order_success.php';
require_once 'includes/header.php';
?>
<div class="success-page">
  <div class="success-card">
    <div class="success-icon">✅</div>
    <h1 class="success-title">Заказ принят!</h1>
    <p class="success-text">
      Заказ №<?= str_pad($order['id'],6,'0',STR_PAD_LEFT) ?><br>
      Подтверждение отправлено на <strong><?= h($order['email']) ?></strong>.<br>
      Менеджер свяжется с вами в течение рабочего дня.
    </p>
    <div class="success-btns">
      <a href="index.php" class="btn btn-primary btn-lg">На главную</a>
      <?php if (isLoggedIn()): ?><a href="account.php?tab=orders" class="btn btn-outline btn-lg">Мои заказы</a><?php endif; ?>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
