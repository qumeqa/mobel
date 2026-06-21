<?php
// ============================================
// MÖBEL — Configuration
// ============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'furniture_shop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'MÖBEL');
define('SITE_URL', 'http://localhost/furniture-shop');
define('CURRENCY', '₽');

// ---- PDO Connection ----
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:monospace;padding:2rem;color:#c00">DB Error: ' . htmlspecialchars($e->getMessage()) . '</div>');
        }
    }
    return $pdo;
}

// ---- Session ----
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Helpers ----
function price(float $p): string {
    return number_format($p, 0, ',', ' ') . ' ' . CURRENCY;
}

function slug(string $str): string {
    $str = mb_strtolower($str);
    $str = strtr($str, ['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
        'ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m','н'=>'n',
        'о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h',
        'ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e',
        'ю'=>'yu','я'=>'ya',' '=>'-']);
    return preg_replace('/[^a-z0-9\-]/', '', $str);
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function isAdmin(): bool {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

// ---- Cart ----
function cartItems(): array {
    return $_SESSION['cart'] ?? [];
}

function cartCount(): int {
    return array_sum(array_column(cartItems(), 'qty'));
}

function cartTotal(): float {
    $total = 0;
    foreach (cartItems() as $item) {
        $total += $item['price'] * $item['qty'];
    }
    return $total;
}

function cartAdd(int $id, string $name, float $price, int $qty = 1): void {
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = ['id'=>$id,'name'=>$name,'price'=>$price,'qty'=>0];
    }
    $_SESSION['cart'][$id]['qty'] += $qty;
}

function cartRemove(int $id): void {
    unset($_SESSION['cart'][$id]);
}

function cartClear(): void {
    $_SESSION['cart'] = [];
}
