<?php
require_once '../includes/config.php';
if (!isAdmin()) redirect('../login.php');
if (isset($_GET['approve'])) { db()->prepare("UPDATE reviews SET approved=1 WHERE id=?")->execute([(int)$_GET['approve']]); redirect('reviews.php'); }
if (isset($_GET['delete']))  { db()->prepare("DELETE FROM reviews WHERE id=?")->execute([(int)$_GET['delete']]);  redirect('reviews.php'); }
$reviews=db()->query("SELECT r.*,p.name AS pname FROM reviews r JOIN products p ON p.id=r.product_id ORDER BY r.approved ASC,r.id DESC")->fetchAll();
$pageTitle='Отзывы'; $currentPage='admin/reviews.php';
require_once '../includes/header_admin.php';
?>
<div class="admin-wrap">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <h1 class="admin-page-title">Отзывы</h1>
    <div class="admin-table-wrap"><table class="data-table">
      <thead><tr><th>Товар</th><th>Автор</th><th>Оценка</th><th>Текст</th><th>Дата</th><th>Статус</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($reviews as $r): ?>
      <tr>
        <td><?=h($r['pname'])?></td><td><?=h($r['author'])?></td>
        <td style="color:#d4a017"><?=str_repeat('★',(int)$r['rating'])?></td>
        <td class="td-muted" style="max-width:220px"><?=h(mb_substr($r['text'],0,80)).(mb_strlen($r['text'])>80?'…':'')?></td>
        <td class="td-muted"><?=date('d.m.Y',strtotime($r['created_at']))?></td>
        <td><?=$r['approved']?'<span class="badge badge-shipped">Опубликован</span>':'<span class="badge badge-pending">На модерации</span>'?></td>
        <td style="display:flex;gap:6px;padding:10px 16px">
          <?php if (!$r['approved']): ?><a href="reviews.php?approve=<?=$r['id']?>" class="btn btn-ghost btn-sm">Опубл.</a><?php endif; ?>
          <a href="reviews.php?delete=<?=$r['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить?')">Удал.</a>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?php require_once '../includes/footer_admin.php'; ?>
