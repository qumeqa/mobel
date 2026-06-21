<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_to_cart'])) {
    cartAdd((int)$_POST['product_id'],$_POST['product_name'],(float)$_POST['product_price']);
    $_SESSION['flash']=['type'=>'success','msg'=>'Товар добавлен в корзину'];
    redirect($_SERVER['REQUEST_URI']);
}

$cat    = trim($_GET['cat']    ?? '');
$brands = isset($_GET['brand']) ? array_filter(array_map('trim',(array)$_GET['brand'])) : [];
$minP   = (int)($_GET['min_price'] ?? 0);
$maxP   = (int)($_GET['max_price'] ?? 0);
$isNew  = !empty($_GET['new']) ? 1 : 0;
$sort   = $_GET['sort'] ?? 'id_desc';
$page   = max(1,(int)($_GET['page'] ?? 1));
$perPage = 12;

$where=['1=1']; $params=[];
if ($cat)    { $where[]='c.slug=?'; $params[]=$cat; }
if ($brands) { $ph=implode(',',array_fill(0,count($brands),'?')); $where[]="b.slug IN($ph)"; $params=array_merge($params,array_values($brands)); }
if ($minP)   { $where[]='p.price>=?'; $params[]=$minP; }
if ($maxP)   { $where[]='p.price<=?'; $params[]=$maxP; }
if ($isNew)  { $where[]='p.is_new=1'; }

$sortMap=['id_desc'=>'p.id DESC','price_asc'=>'p.price ASC','price_desc'=>'p.price DESC','name_asc'=>'p.name ASC'];
$orderBy=$sortMap[$sort]??'p.id DESC';
$whereStr=implode(' AND ',$where);

$cnt=db()->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON c.id=p.category_id JOIN brands b ON b.id=p.brand_id WHERE $whereStr");
$cnt->execute($params); $total=(int)$cnt->fetchColumn();
$pages=max(1,(int)ceil($total/$perPage)); $page=min($page,$pages); $offset=($page-1)*$perPage;

$ps=db()->prepare("SELECT p.*,c.name AS cat_name,c.slug AS cat_slug,b.name AS brand_name FROM products p JOIN categories c ON c.id=p.category_id JOIN brands b ON b.id=p.brand_id WHERE $whereStr ORDER BY $orderBy LIMIT $perPage OFFSET $offset");
$ps->execute($params); $products=$ps->fetchAll();

$allCats   = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$allBrands = db()->query("SELECT * FROM brands ORDER BY name")->fetchAll();

if ($cat) { $s=db()->prepare("SELECT name FROM categories WHERE slug=?"); $s->execute([$cat]); $catName=$s->fetchColumn()?:'Каталог'; } else { $catName='Каталог'; }

// AJAX
if (!empty($_GET['ajax'])) {
    ob_start();
    if ($products): ?>
    <div class="catalog-grid" id="products-grid">
        <?php foreach ($products as $p): include 'includes/product_card.php'; endforeach; ?>
    </div>
    <?php if ($pages>1): ?>
    <div class="pagination" id="pagination">
        <?php for($i=1;$i<=$pages;$i++): ?><a href="#" class="page-link <?=$i===$page?'active':''?>" data-page="<?=$i?>"><?=$i?></a><?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="empty-state"><div class="empty-state__icon">🔍</div><div class="empty-state__title">Товары не найдены</div><p>Попробуйте изменить фильтры</p></div>
    <?php endif;
    $html=ob_get_clean();
    header('Content-Type: application/json');
    echo json_encode(['html'=>$html,'total'=>$total,'catName'=>$catName]);
    exit;
}

$pageTitle=$catName; $currentPage='catalog.php';
require_once 'includes/header.php';
?>

<div class="breadcrumb">
  <a href="index.php">Главная</a><span class="breadcrumb-sep">/</span>
  <span id="breadcrumb-cat"><?= h($catName) ?></span>
</div>

<div class="catalog-layout">
  <!-- SIDEBAR -->
  <aside class="filters">
    <div class="filters-section">
      <div class="filters-title">Категории</div>
      <label class="filters-option">
        <input type="radio" name="cat" value="" <?= $cat===''?'checked':'' ?>> Все категории
      </label>
      <?php foreach ($allCats as $fc): ?>
      <label class="filters-option">
        <input type="radio" name="cat" value="<?= h($fc['slug']) ?>" <?= $cat===$fc['slug']?'checked':'' ?>>
        <?= h($fc['name']) ?>
      </label>
      <?php endforeach; ?>
    </div>
    <div class="filters-section">
      <div class="filters-title">Бренд</div>
      <?php foreach ($allBrands as $fb): ?>
      <label class="filters-option">
        <input type="checkbox" name="brand[]" value="<?= h($fb['slug']) ?>" <?= in_array($fb['slug'],$brands,true)?'checked':'' ?>>
        <?= h($fb['name']) ?>
        <span><?= h($fb['country']) ?></span>
      </label>
      <?php endforeach; ?>
    </div>
    <div class="filters-section">
      <div class="filters-title">Цена (₽)</div>
      <div class="price-range">
        <input class="price-range-input" type="number" name="min_price" placeholder="от" value="<?= $minP?:'' ?>">
        <span class="price-range-sep">—</span>
        <input class="price-range-input" type="number" name="max_price" placeholder="до" value="<?= $maxP?:'' ?>">
      </div>
    </div>
    <div class="filters-section">
      <label class="filters-option">
        <input type="checkbox" name="new" value="1" <?= $isNew?'checked':'' ?>> Только новинки
      </label>
    </div>
    <div class="filters-section">
      <button id="btn-reset" class="btn-reset-filters">Сбросить фильтры</button>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="catalog-results">
    <div class="catalog-toolbar">
      <span class="catalog-count" id="count-label">Найдено: <?= $total ?> товаров</span>
      <select class="catalog-sort" id="sort-select">
        <option value="id_desc"    <?=$sort==='id_desc'?'selected':''?>>Сначала новые</option>
        <option value="price_asc"  <?=$sort==='price_asc'?'selected':''?>>Цена ↑</option>
        <option value="price_desc" <?=$sort==='price_desc'?'selected':''?>>Цена ↓</option>
        <option value="name_asc"   <?=$sort==='name_asc'?'selected':''?>>По названию</option>
      </select>
    </div>
    <div id="catalog-results" style="transition:opacity .15s">
      <?php if ($products): ?>
      <div class="catalog-grid" id="products-grid">
        <?php foreach ($products as $p): include 'includes/product_card.php'; endforeach; ?>
      </div>
      <?php if ($pages>1): ?>
      <div class="pagination" id="pagination">
        <?php for($i=1;$i<=$pages;$i++): ?><a href="#" class="page-link <?=$i===$page?'active':''?>" data-page="<?=$i?>"><?=$i?></a><?php endfor; ?>
      </div>
      <?php endif; ?>
      <?php else: ?>
      <div class="empty-state"><div class="empty-state__icon">🔍</div><div class="empty-state__title">Товары не найдены</div></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
(function(){
  const state={cat:<?=json_encode($cat)?>,brands:<?=json_encode(array_values($brands))?>,min_price:<?=json_encode($minP?:'')
?>,max_price:<?=json_encode($maxP?:'')?>,new:<?=json_encode($isNew?'1':'')?>,sort:<?=json_encode($sort)?>,page:1};
  const results=document.getElementById('catalog-results');
  const countLbl=document.getElementById('count-label');
  const breadcat=document.getElementById('breadcrumb-cat');

  function buildQS(extra){
    const p=new URLSearchParams();
    if(state.cat) p.set('cat',state.cat);
    state.brands.forEach(b=>p.append('brand[]',b));
    if(state.min_price) p.set('min_price',state.min_price);
    if(state.max_price) p.set('max_price',state.max_price);
    if(state.new) p.set('new','1');
    if(state.sort!=='id_desc') p.set('sort',state.sort);
    if(state.page>1) p.set('page',state.page);
    if(extra) Object.entries(extra).forEach(([k,v])=>p.set(k,v));
    return p.toString();
  }

  let t=null;
  function load(instant){
    clearTimeout(t);
    t=setTimeout(()=>{
      results.style.opacity='.4';
      fetch('catalog.php?'+buildQS({ajax:'1'}))
        .then(r=>r.json()).then(d=>{
          results.innerHTML=d.html;
          results.style.opacity='1';
          countLbl.textContent='Найдено: '+d.total+' товаров';
          breadcat.textContent=d.catName;
          bindPagination();
          history.replaceState(null,'','catalog.php'+(buildQS()?'?'+buildQS():''));
        });
    },instant?0:400);
  }

  function bindPagination(){
    results.querySelectorAll('.page-link[data-page]').forEach(a=>{
      a.addEventListener('click',e=>{e.preventDefault();state.page=+a.dataset.page;load(true);});
    });
  }
  bindPagination();

  document.querySelectorAll('input[name="cat"]').forEach(r=>r.addEventListener('change',()=>{state.cat=r.value;state.page=1;load(true);}));
  document.querySelectorAll('input[name="brand[]"]').forEach(c=>c.addEventListener('change',()=>{state.brands=[...document.querySelectorAll('input[name="brand[]"]:checked')].map(x=>x.value);state.page=1;load(true);}));
  document.querySelector('input[name="min_price"]').addEventListener('input',e=>{state.min_price=e.target.value;state.page=1;load(false);});
  document.querySelector('input[name="max_price"]').addEventListener('input',e=>{state.max_price=e.target.value;state.page=1;load(false);});
  document.querySelector('input[name="new"]').addEventListener('change',e=>{state.new=e.target.checked?'1':'';state.page=1;load(true);});
  document.getElementById('sort-select').addEventListener('change',e=>{state.sort=e.target.value;state.page=1;load(true);});
  document.getElementById('btn-reset').addEventListener('click',()=>{
    document.querySelectorAll('input[name="brand[]"]').forEach(c=>c.checked=false);
    document.querySelectorAll('input[name="cat"]').forEach(r=>r.checked=r.value==='');
    document.querySelector('input[name="min_price"]').value='';
    document.querySelector('input[name="max_price"]').value='';
    document.querySelector('input[name="new"]').checked=false;
    Object.assign(state,{cat:'',brands:[],min_price:'',max_price:'',new:'',page:1});
    load(true);
  });
})();
</script>

<?php require_once 'includes/footer.php'; ?>
