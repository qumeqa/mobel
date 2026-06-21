<?php
require_once '../includes/config.php';
if (!isAdmin()) redirect('../login.php');
if (isset($_GET['delete'])) { db()->prepare("DELETE FROM products WHERE id=?")->execute([(int)$_GET['delete']]); $_SESSION['flash']=['type'=>'success','msg'=>'Товар удалён']; redirect('products.php'); }
$editing=null;
if (isset($_GET['edit'])) { if ((int)$_GET['edit']>0) { $s=db()->prepare("SELECT * FROM products WHERE id=?"); $s->execute([(int)$_GET['edit']]); $editing=$s->fetch()?:[]; } else $editing=[]; }
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save'])) {
    $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $cat=(int)$_POST['category_id']; $brand=(int)$_POST['brand_id'];
    $price=(float)$_POST['price']; $oldp=$_POST['old_price']?(float)$_POST['old_price']:null;
    $desc=trim($_POST['description']??''); $stock=(int)$_POST['stock'];
    $isN=isset($_POST['is_new'])?1:0; $isF=isset($_POST['is_featured'])?1:0;
    $sp=[]; foreach(explode("\n",$_POST['specs']??'') as $l){$pts=explode(':',$l,2);if(count($pts)===2)$sp[trim($pts[0])]=trim($pts[1]);}
    $spj=json_encode($sp,JSON_UNESCAPED_UNICODE);
    if (!$name) { $_SESSION['flash']=['type'=>'error','msg'=>'Введите название']; redirect('products.php?edit='.$id); }
    // Обработка загруженного фото
    $imageData = null; $imageMime = null;
    if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageMime = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($imageMime, ['image/jpeg','image/png','image/webp','image/gif'])) {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
        }
    }
    if ($id) {
        if ($imageData) {
            db()->prepare("UPDATE products SET name=?,category_id=?,brand_id=?,price=?,old_price=?,description=?,specs=?,stock=?,is_new=?,is_featured=?,image=?,image_type=? WHERE id=?")->execute([$name,$cat,$brand,$price,$oldp,$desc,$spj,$stock,$isN,$isF,$imageData,$imageMime,$id]);
        } else {
            db()->prepare("UPDATE products SET name=?,category_id=?,brand_id=?,price=?,old_price=?,description=?,specs=?,stock=?,is_new=?,is_featured=? WHERE id=?")->execute([$name,$cat,$brand,$price,$oldp,$desc,$spj,$stock,$isN,$isF,$id]);
        }
        $_SESSION['flash']=['type'=>'success','msg'=>'Обновлено'];
    } else {
        db()->prepare("INSERT INTO products(name,slug,category_id,brand_id,price,old_price,description,specs,stock,is_new,is_featured,image,image_type) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([$name,slug($name).'-'.time(),$cat,$brand,$price,$oldp,$desc,$spj,$stock,$isN,$isF,$imageData,$imageMime]);
        $_SESSION['flash']=['type'=>'success','msg'=>'Добавлено'];
    }
    redirect('products.php');
}
$products=db()->query("SELECT p.*,c.name AS cn FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
$cats=db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$brands=db()->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$pageTitle='Товары'; $currentPage='admin/products.php';
require_once '../includes/header_admin.php';
?>
<div class="admin-wrap">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-toolbar">
      <h1 class="admin-page-title" style="margin:0">Товары</h1>
      <a href="products.php?edit=0" class="btn btn-primary">+ Добавить товар</a>
    </div>

    <?php if (isset($_GET['edit'])): ?>
    <div class="admin-form-box">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <div class="admin-form-title" style="margin:0"><?= !empty($editing['id']) ? 'Редактировать: '.h($editing['name']) : 'Новый товар' ?></div>
        <?php if (!empty($editing['image'])): ?>
        <img src="../image.php?id=<?= (int)$editing['id'] ?>" style="width:80px;height:80px;object-fit:cover;border-radius:var(--r-md);border:1px solid var(--c-border)">
        <?php endif; ?>
      </div>
      <form method="post" class="form-stack" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?=(int)($editing['id']??0)?>">
        <div class="admin-form-grid">
          <div class="field"><label class="field-label">Название *</label><input class="field-input" type="text" name="name" value="<?=h($editing['name']??'')?>" required></div>
          <div class="field"><label class="field-label">Цена</label><input class="field-input" type="number" name="price" step="0.01" value="<?=$editing['price']??''?>"></div>
          <div class="field"><label class="field-label">Старая цена</label><input class="field-input" type="number" name="old_price" step="0.01" value="<?=$editing['old_price']??''?>"></div>
          <div class="field"><label class="field-label">Категория</label>
            <select class="field-select" name="category_id"><?php foreach($cats as $c): ?><option value="<?=$c['id']?>" <?=($editing['category_id']??'')==$c['id']?'selected':''?>><?=h($c['name'])?></option><?php endforeach; ?></select></div>
          <div class="field"><label class="field-label">Бренд</label>
            <select class="field-select" name="brand_id"><?php foreach($brands as $b): ?><option value="<?=$b['id']?>" <?=($editing['brand_id']??'')==$b['id']?'selected':''?>><?=h($b['name'])?></option><?php endforeach; ?></select></div>
          <div class="field"><label class="field-label">Остаток</label><input class="field-input" type="number" name="stock" value="<?=$editing['stock']??0?>"></div>
          <div class="field"><label class="field-label">Фото товара</label><input class="field-input" type="file" name="image" accept="image/*" style="padding:8px"><span class="field-hint"><?= !empty($editing['image']) ? 'Фото загружено. Выберите новое для замены.' : 'Выберите изображение (JPEG, PNG, WebP)' ?></span></div>
        </div>
        <div class="field"><label class="field-label">Описание</label><textarea class="field-textarea" name="description" rows="3"><?=h($editing['description']??'')?></textarea></div>
        <div class="field"><label class="field-label">Характеристики (Ключ: Значение, каждая с новой строки)</label>
          <textarea class="field-textarea" name="specs" rows="5" placeholder="Ширина: 200 см&#10;Материал: Дуб"><?php if(!empty($editing['specs'])){$sp=json_decode($editing['specs'],true);if($sp)foreach($sp as $k=>$v)echo h($k.': '.$v)."\n";}?></textarea></div>
        <div style="display:flex;gap:20px">
          <label style="display:flex;align-items:center;gap:6px;font-size:var(--fs-sm)"><input type="checkbox" name="is_new" <?=!empty($editing['is_new'])?'checked':''?>> Новинка</label>
          <label style="display:flex;align-items:center;gap:6px;font-size:var(--fs-sm)"><input type="checkbox" name="is_featured" <?=!empty($editing['is_featured'])?'checked':''?>> Рекомендуем</label>
        </div>
        <div style="display:flex;gap:8px">
          <button type="submit" name="save" class="btn btn-primary">Сохранить</button>
          <a href="products.php" class="btn btn-outline">Отмена</a>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <div class="admin-table-wrap"><table class="data-table">
      <thead><tr><th>ID</th><th>Название</th><th>Категория</th><th>Цена</th><th>Остаток</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td class="td-mono td-muted"><?=$p['id']?></td>
        <td><a href="../product.php?slug=<?=h($p['slug'])?>" style="font-weight:500"><?=h($p['name'])?></a></td>
        <td class="td-muted"><?=h($p['cn'])?></td>
        <td class="td-mono"><?=price((float)$p['price'])?></td>
        <td class="td-mono"><?=(int)$p['stock']?></td>
        <td style="display:flex;gap:6px;padding:10px 16px">
          <a href="products.php?edit=<?=$p['id']?>" class="btn btn-ghost btn-sm">Изм.</a>
          <a href="products.php?delete=<?=$p['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить товар?')">Удал.</a>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?php require_once '../includes/footer_admin.php'; ?>
