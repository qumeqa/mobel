<?php
require_once '../includes/config.php';
if (!isAdmin()) redirect('../login.php');
$users=db()->query("SELECT u.*,COUNT(o.id) AS oc FROM users u LEFT JOIN orders o ON o.user_id=u.id WHERE u.role='customer' GROUP BY u.id ORDER BY u.id DESC")->fetchAll();
$pageTitle='Клиенты'; $currentPage='admin/users.php';
require_once '../includes/header_admin.php';
?>
<div class="admin-wrap">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <h1 class="admin-page-title">Клиенты</h1>
    <div class="admin-table-wrap"><table class="data-table">
      <thead><tr><th>ID</th><th>Имя</th><th>Email</th><th>Телефон</th><th>Заказов</th><th>Дата</th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td class="td-mono td-muted"><?=$u['id']?></td>
        <td><?=h($u['name'])?></td><td class="td-muted"><?=h($u['email'])?></td>
        <td><?=h($u['phone']?:'—')?></td><td class="td-mono"><?=(int)$u['oc']?></td>
        <td class="td-muted"><?=date('d.m.Y',strtotime($u['created_at']))?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$users): ?><tr><td colspan="6" style="text-align:center;padding:32px;color:var(--c-muted)">Клиентов пока нет</td></tr><?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?php require_once '../includes/footer_admin.php'; ?>
