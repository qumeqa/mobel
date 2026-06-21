<?php $ap = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar">
  <div class="admin-sidebar-logo"><?= SITE_NAME ?></div>
  <nav>
    <div class="admin-nav-group">Управление</div>
    <a href="index.php"    class="admin-nav-link <?= $ap==='index.php'?'is-active':'' ?>">Dashboard</a>
    <a href="products.php" class="admin-nav-link <?= $ap==='products.php'?'is-active':'' ?>">Товары</a>
    <a href="orders.php"   class="admin-nav-link <?= $ap==='orders.php'?'is-active':'' ?>">Заказы</a>
    <a href="reviews.php"  class="admin-nav-link <?= $ap==='reviews.php'?'is-active':'' ?>">Отзывы</a>
    <a href="users.php"    class="admin-nav-link <?= $ap==='users.php'?'is-active':'' ?>">Клиенты</a>
    <div class="admin-nav-group" style="margin-top:20px"></div>
    <a href="../logout.php" class="admin-nav-link admin-nav-link--danger">Выйти</a>
  </nav>
</aside>
