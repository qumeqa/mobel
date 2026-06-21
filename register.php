<?php
require_once 'includes/config.php';
if (isLoggedIn()) redirect('account.php');
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??'');
    $pass=$_POST['password']??''; $pass2=$_POST['password2']??'';
    if (!$name) $errors['name']='Введите имя';
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors['email']='Некорректный email';
    if (strlen($pass)<6) $errors['password']='Минимум 6 символов';
    if ($pass!==$pass2) $errors['password2']='Пароли не совпадают';
    if (!$errors) { $ex=db()->prepare("SELECT id FROM users WHERE email=?"); $ex->execute([$email]); if ($ex->fetch()) $errors['email']='Email уже занят'; }
    if (!$errors) {
        $ins=db()->prepare("INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)");
        $ins->execute([$name,$email,$phone,password_hash($pass,PASSWORD_BCRYPT)]);
        $s=db()->prepare("SELECT * FROM users WHERE id=?"); $s->execute([db()->lastInsertId()]); $_SESSION['user']=$s->fetch();
        $_SESSION['flash']=['type'=>'success','msg'=>'Добро пожаловать, '.$name.'!']; redirect('account.php');
    }
}
$pageTitle = 'Регистрация'; $currentPage = 'register.php';
require_once 'includes/header.php';
?>
<div class="auth-page">
  <div class="auth-card">
    <h1 class="auth-title">Регистрация</h1>
    <p class="auth-sub">Создайте аккаунт для отслеживания заказов</p>
    <form method="post" class="form-stack">
      <div class="field"><label class="field-label">Имя *</label><input class="field-input" type="text" name="name" required value="<?= h($_POST['name']??'') ?>">
        <?php if (isset($errors['name'])): ?><span class="field-error"><?= h($errors['name']) ?></span><?php endif; ?></div>
      <div class="field"><label class="field-label">Email *</label><input class="field-input" type="email" name="email" required value="<?= h($_POST['email']??'') ?>">
        <?php if (isset($errors['email'])): ?><span class="field-error"><?= h($errors['email']) ?></span><?php endif; ?></div>
      <div class="field"><label class="field-label">Телефон</label><input class="field-input" type="tel" name="phone" value="<?= h($_POST['phone']??'') ?>"></div>
      <div class="form-row">
        <div class="field"><label class="field-label">Пароль *</label><input class="field-input" type="password" name="password" required>
          <?php if (isset($errors['password'])): ?><span class="field-error"><?= h($errors['password']) ?></span><?php endif; ?></div>
        <div class="field"><label class="field-label">Повтор *</label><input class="field-input" type="password" name="password2" required>
          <?php if (isset($errors['password2'])): ?><span class="field-error"><?= h($errors['password2']) ?></span><?php endif; ?></div>
      </div>
      <button type="submit" class="btn btn-primary btn-full btn-lg">Создать аккаунт</button>
    </form>
    <p class="auth-footer">Есть аккаунт? <a href="login.php">Войти</a></p>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
