<?php
require_once 'includes/config.php';
if (!isLoggedIn()) redirect('login.php');
$user = currentUser(); $tab = $_GET['tab'] ?? 'orders'; $errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_profile'])) {
    $name=trim($_POST['name']??''); $phone=trim($_POST['phone']??'');
    if (!$name) $errors['name']='Введите имя';
    if (!$errors) { db()->prepare("UPDATE users SET name=?,phone=? WHERE id=?")->execute([$name,$phone,$user['id']]); $s=db()->prepare("SELECT * FROM users WHERE id=?"); $s->execute([$user['id']]); $_SESSION['user']=$s->fetch(); $_SESSION['flash']=['type'=>'success','msg'=>'Профиль обновлён']; redirect('account.php?tab=profile'); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_pass'])) {
    $old=$_POST['old']??''; $new=$_POST['new']??''; $new2=$_POST['new2']??'';
    if (!password_verify($old,$user['password'])) $errors['old']='Неверный пароль';
    if (strlen($new)<6) $errors['new']='Минимум 6 символов';
    if ($new!==$new2) $errors['new2']='Пароли не совпадают';
    if (!$errors) { db()->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new,PASSWORD_BCRYPT),$user['id']]); $_SESSION['flash']=['type'=>'success','msg'=>'Пароль изменён']; redirect('account.php?tab=profile'); }
}
$orders = db()->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY id DESC"); $orders->execute([$user['id']]); $orders = $orders->fetchAll();
$sl = ['pending'=>'Ожидает','confirmed'=>'Подтверждён','shipped'=>'В пути','delivered'=>'Доставлен','cancelled'=>'Отменён'];
$bc = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
$pageTitle = 'Личный кабинет'; $currentPage = 'account.php';
require_once 'includes/header.php';
?>
<div class="breadcrumb"><a href="index.php">Главная</a><span class="breadcrumb-sep">/</span><span>Кабинет</span></div>
<div class="account-layout">
  <nav class="account-nav">
    <a href="account.php?tab=orders"   class="account-nav-link <?= $tab==='orders'?'is-active':'' ?>">Мои заказы</a>
    <a href="account.php?tab=profile"  class="account-nav-link <?= $tab==='profile'?'is-active':'' ?>">Профиль</a>
    <a href="account.php?tab=password" class="account-nav-link <?= $tab==='password'?'is-active':'' ?>">Пароль</a>
    <a href="logout.php" class="account-nav-link account-nav-link--danger">Выйти</a>
  </nav>
  <div class="account-content">

    <?php if ($tab === 'orders'): ?>
    <h1 class="account-page-title">Мои заказы</h1>
    <?php if ($orders): ?>
    <div class="order-list">
      <?php foreach ($orders as $o): ?>
      <div class="order-card">
        <div>
          <div class="order-card__num">Заказ №<?= str_pad($o['id'],6,'0',STR_PAD_LEFT) ?> · <?= date('d.m.Y',strtotime($o['created_at'])) ?></div>
          <div class="order-card__meta">
            <span class="order-card__total"><?= price((float)$o['total']) ?></span>
            <span class="badge <?= $bc[$o['status']]??'' ?>"><?= $sl[$o['status']]??$o['status'] ?></span>
            <span class="order-card__date"><?= $o['delivery_method']==='pickup'?'Самовывоз':'Курьер' ?></span>
          </div>
        </div>
        <a href="order_detail.php?id=<?= $o['id'] ?>" class="btn btn-outline btn-sm">Детали</a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-box" style="text-align:left;padding:0">
      <div class="empty-box__icon">📦</div>
      <div class="empty-box__title">Заказов пока нет</div>
      <p class="empty-box__text">Перейдите в каталог и выберите товары</p>
      <a href="catalog.php" class="btn btn-primary">Перейти в каталог</a>
    </div>
    <?php endif; ?>

    <?php elseif ($tab === 'profile'): ?>
    <h1 class="account-page-title">Профиль</h1>
    <div class="profile-form">
      <form method="post" class="form-stack">
        <div class="field"><label class="field-label">Имя</label><input class="field-input" type="text" name="name" value="<?= h($user['name']) ?>" required>
          <?php if (isset($errors['name'])): ?><span class="field-error"><?= h($errors['name']) ?></span><?php endif; ?></div>
        <div class="field"><label class="field-label">Email</label><input class="field-input" value="<?= h($user['email']) ?>" disabled style="opacity:.55"></div>
        <div class="field"><label class="field-label">Телефон</label><input class="field-input" type="tel" name="phone" value="<?= h($user['phone']??'') ?>"></div>
        <div><button type="submit" name="save_profile" class="btn btn-primary">Сохранить изменения</button></div>
      </form>
    </div>

    <?php elseif ($tab === 'password'): ?>
    <h1 class="account-page-title">Смена пароля</h1>
    <div class="profile-form">
      <form method="post" class="form-stack">
        <div class="field"><label class="field-label">Текущий пароль</label><input class="field-input" type="password" name="old" required>
          <?php if (isset($errors['old'])): ?><span class="field-error"><?= h($errors['old']) ?></span><?php endif; ?></div>
        <div class="field"><label class="field-label">Новый пароль</label><input class="field-input" type="password" name="new" required>
          <?php if (isset($errors['new'])): ?><span class="field-error"><?= h($errors['new']) ?></span><?php endif; ?></div>
        <div class="field"><label class="field-label">Повтор нового пароля</label><input class="field-input" type="password" name="new2" required>
          <?php if (isset($errors['new2'])): ?><span class="field-error"><?= h($errors['new2']) ?></span><?php endif; ?></div>
        <div><button type="submit" name="save_pass" class="btn btn-primary">Изменить пароль</button></div>
      </form>
    </div>
    <?php endif; ?>

  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
