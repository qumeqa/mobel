<?php
require_once '../includes/config.php';
if (!isAdmin()) redirect('../login.php');
$stats = ['orders'=>db()->query("SELECT COUNT(*) FROM orders")->fetchColumn(),'products'=>db()->query("SELECT COUNT(*) FROM products")->fetchColumn(),'users'=>db()->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),'revenue'=>db()->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status!='cancelled'")->fetchColumn()];
$recent = db()->query("SELECT * FROM orders ORDER BY id DESC LIMIT 10")->fetchAll();
$sl=['pending'=>'Ожидает','confirmed'=>'Подтверждён','shipped'=>'В пути','delivered'=>'Доставлен','cancelled'=>'Отменён'];
$bc=['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
$pageTitle='Dashboard'; $currentPage='admin/index.php';
require_once '../includes/header_admin.php';
?>
<div class="admin-wrap">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <h1 class="admin-page-title">Dashboard</h1>
    <div class="admin-stats">
      <div class="stat-card"><div class="stat-num"><?=(int)$stats['orders']?></div><div class="stat-label">Заказов</div></div>
      <div class="stat-card"><div class="stat-num"><?=(int)$stats['products']?></div><div class="stat-label">Товаров</div></div>
      <div class="stat-card"><div class="stat-num"><?=(int)$stats['users']?></div><div class="stat-label">Клиентов</div></div>
      <div class="stat-card"><div class="stat-num"><?=number_format($stats['revenue'],0,' ')?></div><div class="stat-label">Выручка ₽</div></div>
    </div>
    <h2 style="font-family:var(--f-display);font-size:20px;font-weight:700;margin-bottom:14px">Последние заказы</h2>
    <div class="admin-table-wrap"><table class="data-table">
      <thead><tr><th>№</th><th>Клиент</th><th>Email</th><th>Сумма</th><th>Дата</th><th>Статус</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($recent as $o): ?>
      <tr>
        <td class="td-mono"><?=str_pad($o['id'],6,'0',STR_PAD_LEFT)?></td>
        <td><?=h($o['name'])?></td><td class="td-muted"><?=h($o['email'])?></td>
        <td class="td-mono"><?=price((float)$o['total'])?></td>
        <td class="td-muted"><?=date('d.m.Y',strtotime($o['created_at']))?></td>
        <td><span class="badge <?=$bc[$o['status']]??''?>"><?=$sl[$o['status']]??$o['status']?></span></td>
        <td><a href="orders.php?edit=<?=$o['id']?>" class="btn btn-ghost btn-sm">Изм.</a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?php require_once '../includes/footer_admin.php'; ?>
