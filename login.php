<?php
require_once 'includes/config.php';
if (isLoggedIn()) redirect('account.php');
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = db()->prepare("SELECT * FROM users WHERE email=?"); $s->execute([trim($_POST['email']??'')]); $u = $s->fetch();
    if ($u && password_verify($_POST['password']??'', $u['password'])) { $_SESSION['user'] = $u; redirect($u['role']==='admin' ? 'admin/index.php' : 'account.php'); }
    else $errors[] = 'Неверный email или пароль';
}
$pageTitle = 'Вход'; $currentPage = 'login.php';
require_once 'includes/header.php';
?>
<div class="auth-page">
  <div class="auth-card">
    <h1 class="auth-title">Вход</h1>
    <p class="auth-sub">Войдите в личный кабинет</p>
    <?php foreach ($errors as $e): ?><div class="flash flash-error" style="border-radius:var(--r-md);margin-bottom:16px;padding:10px 14px"><?= h($e) ?></div><?php endforeach; ?>
    <form method="post" class="form-stack">
      <div class="field"><label class="field-label">Email</label><input class="field-input" type="email" name="email" required value="<?= h($_POST['email']??'') ?>"></div>
      <div class="field"><label class="field-label">Пароль</label><input class="field-input" type="password" name="password" required></div>
      <button type="submit" class="btn btn-primary btn-full btn-lg">Войти</button>
    </form>
    <p class="auth-footer">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
