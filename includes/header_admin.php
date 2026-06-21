<?php
$user        = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? h($pageTitle).' — Админ — '.SITE_NAME : 'Админ — '.SITE_NAME ?></title>
<link rel="stylesheet" href="../css/tokens.css">
<link rel="stylesheet" href="../css/base.css">
<link rel="stylesheet" href="../css/components.css">
<link rel="stylesheet" href="../css/layout.css">
<link rel="stylesheet" href="../css/pages.css">
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<header class="admin-header">
  <div class="admin-header-wrap">
    <a href="../index.php" class="admin-header-logo"><?= SITE_NAME ?></a>
    <span class="admin-header-badge">Панель управления</span>
    <div class="admin-header-right">
      <a href="../index.php" class="admin-header-link">← На сайт</a>
      <span class="admin-header-user"><?= h($user['name'] ?? '') ?></span>
      <a href="../logout.php" class="admin-header-link admin-header-link--danger">Выйти</a>
    </div>
  </div>
</header>

<?php if (!empty($_SESSION['flash'])):
  $fl = $_SESSION['flash']; unset($_SESSION['flash']); ?>
<div class="flash flash-<?= h($fl['type']) ?>"><?= h($fl['msg']) ?></div>
<?php endif; ?>

<main>
