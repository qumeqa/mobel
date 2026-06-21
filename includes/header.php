<?php
$cartCount   = cartCount();
$user        = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
$cats        = db()->query("SELECT name, slug FROM categories ORDER BY id")->fetchAll();
$isAdminDir  = str_contains($_SERVER['PHP_SELF'], '/admin/');
$d           = $isAdminDir ? '../' : '';
$activeCat   = trim($_GET['cat'] ?? '');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? h($pageTitle).' — '.SITE_NAME : SITE_NAME.' — Мебель' ?></title>
<link rel="stylesheet" href="<?= $d ?>css/tokens.css">
<link rel="stylesheet" href="<?= $d ?>css/base.css">
<link rel="stylesheet" href="<?= $d ?>css/components.css">
<link rel="stylesheet" href="<?= $d ?>css/layout.css">
<link rel="stylesheet" href="<?= $d ?>css/pages.css">
<?php if ($isAdminDir): ?><link rel="stylesheet" href="<?= $d ?>css/admin.css"><?php endif; ?>
</head>
<body>

<header class="site-header">
  <div class="header-wrap">

    <a href="<?= $d ?>index.php" class="site-logo"><?= SITE_NAME ?></a>

    <a href="<?= $d ?>catalog.php"
       class="nav-catalog <?= ($currentPage==='catalog.php' && $activeCat==='') ? 'is-active' : '' ?>">
      Каталог
    </a>

    <nav class="header-cats">
      <?php foreach ($cats as $c): ?>
      <a href="<?= $d ?>catalog.php?cat=<?= h($c['slug']) ?>"
         class="header-cat <?= $activeCat===$c['slug'] ? 'is-active' : '' ?>">
        <?= h($c['name']) ?>
      </a>
      <?php endforeach; ?>
    </nav>

    <div class="header-actions">
      <?php if (isLoggedIn()): ?>
        <a href="<?= $d ?>account.php" class="header-link"><?= h($user['name']) ?></a>
        <?php if (isAdmin()): ?>
          <a href="<?= $d ?>admin/index.php" class="header-link">Admin</a>
        <?php endif; ?>
        <a href="<?= $d ?>logout.php" class="header-link">Выйти</a>
      <?php else: ?>
        <a href="<?= $d ?>login.php" class="header-link">Войти</a>
      <?php endif; ?>
      <a href="<?= $d ?>cart.php" class="header-cart">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        Корзина
        <?php if ($cartCount > 0): ?>
          <span class="header-cart-badge"><?= $cartCount ?></span>
        <?php endif; ?>
      </a>
    </div>

  </div>
</header>

<?php if (!empty($_SESSION['flash'])):
  $fl = $_SESSION['flash']; unset($_SESSION['flash']); ?>
<div class="flash flash-<?= h($fl['type']) ?>"><?= h($fl['msg']) ?></div>
<?php endif; ?>

<main>
