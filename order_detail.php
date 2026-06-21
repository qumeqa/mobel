<?php
require_once 'includes/config.php';
$id=(int)($_GET['id']??0); $s=db()->prepare("SELECT * FROM orders WHERE id=?"); $s->execute([$id]); $order=$s->fetch();
if (!$order) redirect('account.php');
if (!isAdmin() && (!isLoggedIn() || $order['user_id']!==currentUser()['id'])) redirect('account.php');
$items=db()->prepare("SELECT * FROM order_items WHERE order_id=?"); $items->execute([$id]); $items=$items->fetchAll();
$sl=['pending'=>'Ожидает','confirmed'=>'Подтверждён','shipped'=>'В пути','delivered'=>'Доставлен','cancelled'=>'Отменён'];
$bc=['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
$pageTitle='Заказ №'.str_pad($order['id'],6,'0',STR_PAD_LEFT); $currentPage='order_detail.php';
require_once 'includes/header.php';
?>
<div class="breadcrumb">
  <a href="index.php">Главная</a><span class="breadcrumb-sep">/</span>
  <a href="account.php?tab=orders">Заказы</a><span class="breadcrumb-sep">/</span>
  <span>№<?= str_pad($order['id'],6,'0',STR_PAD_LEFT) ?></span>
</div>
<div style="max-width:var(--site-width);margin:0 auto;padding:24px var(--page-px) 64px;display:grid;grid-template-columns:1fr 320px;gap:28px;align-items:start">
  <div>
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px">
      <h1 style="font-family:var(--f-display);font-size:26px;font-weight:700">Заказ №<?= str_pad($order['id'],6,'0',STR_PAD_LEFT) ?></h1>
      <span class="badge <?= $bc[$order['status']]??'' ?>"><?= $sl[$order['status']] ?></span>
    </div>
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead><tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th></tr></thead>
        <tbody>
        <?php foreach ($items as $i): ?>
        <tr>
          <td><?= h($i['name']) ?></td>
          <td class="td-mono"><?= price((float)$i['price']) ?></td>
          <td class="td-mono"><?= (int)$i['quantity'] ?></td>
          <td class="td-mono"><?= price($i['price']*$i['quantity']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="summary-box">
    <div class="summary-box__title">Детали заказа</div>
    <?php foreach (['Дата'=>date('d.m.Y H:i',strtotime($order['created_at'])),'Клиент'=>$order['name'],'Email'=>$order['email'],'Телефон'=>$order['phone']?:'—','Доставка'=>$order['delivery_method']==='pickup'?'Самовывоз':'Курьер','Адрес'=>$order['address']?:'—'] as $k=>$v): ?>
    <div class="summary-row">
      <span class="summary-muted"><?= h($k) ?></span>
      <span style="text-align:right;max-width:55%;font-size:var(--fs-sm)"><?= h($v) ?></span>
    </div>
    <?php endforeach; ?>
    <div class="summary-row summary-row--total">
      <span>Итого</span><span class="summary-mono"><?= price((float)$order['total']) ?></span>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
