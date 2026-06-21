<?php
require_once '../includes/config.php';
if (!isAdmin()) redirect('../login.php');
$sl=['pending'=>'Ожидает','confirmed'=>'Подтверждён','shipped'=>'В пути','delivered'=>'Доставлен','cancelled'=>'Отменён'];
$bc=['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])) {
    $st=$_POST['status']; if (array_key_exists($st,$sl)) db()->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$st,(int)$_POST['order_id']]);
    $_SESSION['flash']=['type'=>'success','msg'=>'Статус обновлён']; redirect('orders.php');
}
$editing=null;
if (isset($_GET['edit'])) { $s=db()->prepare("SELECT * FROM orders WHERE id=?"); $s->execute([(int)$_GET['edit']]); $editing=$s->fetch(); }
$orders=db()->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
$pageTitle='Заказы'; $currentPage='admin/orders.php';
require_once '../includes/header_admin.php';
?>
<div class="admin-wrap">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <h1 class="admin-page-title">Заказы</h1>
    <?php if ($editing): ?>
    <div class="admin-form-box" style="max-width:380px">
      <div class="admin-form-title">Заказ #<?=str_pad($editing['id'],6,'0',STR_PAD_LEFT)?></div>
      <form method="post" class="form-stack">
        <input type="hidden" name="order_id" value="<?=$editing['id']?>">
        <div class="field"><label class="field-label">Статус</label>
          <select class="field-select" name="status"><?php foreach ($sl as $v=>$l): ?><option value="<?=$v?>" <?=$editing['status']===$v?'selected':''?>><?=$l?></option><?php endforeach; ?></select>
        </div>
        <div style="display:flex;gap:8px">
          <button type="submit" name="update_status" class="btn btn-primary">Сохранить</button>
          <a href="orders.php" class="btn btn-outline">Отмена</a>
        </div>
      </form>
    </div>
    <?php endif; ?>
    <div class="admin-table-wrap"><table class="data-table">
      <thead><tr><th>№</th><th>Клиент</th><th>Email</th><th>Сумма</th><th>Доставка</th><th>Дата</th><th>Статус</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td class="td-mono"><?=str_pad($o['id'],6,'0',STR_PAD_LEFT)?></td>
        <td><?=h($o['name'])?></td><td class="td-muted"><?=h($o['email'])?></td>
        <td class="td-mono"><?=price((float)$o['total'])?></td>
        <td class="td-muted"><?=$o['delivery_method']==='pickup'?'Самовывоз':'Курьер'?></td>
        <td class="td-muted"><?=date('d.m.Y',strtotime($o['created_at']))?></td>
        <td><span class="badge <?=$bc[$o['status']]??''?>"><?=$sl[$o['status']]?></span></td>
        <td><a href="orders.php?edit=<?=$o['id']?>" class="btn btn-ghost btn-sm">Изм.</a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?php require_once '../includes/footer_admin.php'; ?>
