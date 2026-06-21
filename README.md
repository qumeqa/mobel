# MÖBEL — Интернет-магазин мебели

Швейцарский минимализм. PHP + MySQL + HTML/CSS.

---

## Требования

- PHP 7.4+ (рекомендуется 8.x)
- MySQL 5.7+ или MariaDB 10+
- Apache / Nginx с mod_rewrite
- Опционально: XAMPP / WAMP / Laragon (для Windows) или Homebrew PHP + MySQL (macOS)

---

## Установка (5 шагов)

### 1. Поместите файлы в папку сервера

**XAMPP (Windows):**
```
C:\xampp\htdocs\furniture-shop\
```

**MAMP / Laragon / OpenServer:**
```
[корень проекта]/furniture-shop/
```

**Linux / VPS:**
```
/var/www/html/furniture-shop/
```

---

### 2. Создайте базу данных

Откройте **phpMyAdmin** → http://localhost/phpmyadmin

- Нажмите «Новая» / «New database»
- Имя: `furniture_shop`
- Кодировка: `utf8mb4_unicode_ci`
- Нажмите «Создать»

Затем перейдите в базу `furniture_shop` → вкладка **SQL** → вставьте содержимое файла `database.sql` → нажмите **Выполнить**.

Или через консоль:
```bash
mysql -u root -p -e "CREATE DATABASE furniture_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p furniture_shop < database.sql
```

---

### 3. Настройте подключение к БД

Откройте файл `includes/config.php` и укажите ваши данные:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'furniture_shop');
define('DB_USER', 'root');      // ← ваш пользователь MySQL
define('DB_PASS', '');          // ← ваш пароль MySQL (пусто для XAMPP по умолчанию)
```

---

### 4. Откройте сайт

Перейдите в браузере:
```
http://localhost/furniture-shop/
```

---

### 5. Войдите в панель администратора

URL: `http://localhost/furniture-shop/admin/index.php`

```
Email:    admin@moebel.ru
Пароль:   admin123
```

> **Важно:** После первого входа смените пароль администратора в базе данных или через личный кабинет.

---

## Структура проекта

```
furniture-shop/
├── includes/
│   ├── config.php          ← настройки БД, хелперы, корзина
│   ├── header.php          ← шапка сайта (навигация, стили)
│   ├── footer.php          ← подвал сайта
│   └── product_card.php    ← карточка товара (партиал)
├── admin/
│   ├── index.php           ← дашборд
│   ├── products.php        ← управление товарами
│   ├── orders.php          ← управление заказами
│   ├── reviews.php         ← модерация отзывов
│   ├── users.php           ← список клиентов
│   └── sidebar.php         ← боковое меню
├── uploads/
│   └── products/           ← сюда загружаются фото товаров
├── index.php               ← главная страница
├── catalog.php             ← каталог с фильтрами
├── product.php             ← карточка товара
├── cart.php                ← корзина
├── checkout.php            ← оформление заказа
├── order_success.php       ← страница успешного заказа
├── order_detail.php        ← детали заказа
├── account.php             ← личный кабинет
├── login.php               ← вход
├── register.php            ← регистрация
├── logout.php              ← выход
└── database.sql            ← схема БД + 20 товаров
```

---

## Как добавить фото товаров

1. Поместите изображения в папку `uploads/products/`
2. Имена файлов должны совпадать с полем `image` в таблице `products`
   - Например: `stille3.jpg`, `kivi.jpg`, `tafel160.jpg`
3. Рекомендуемый размер: **800×600 px**, формат JPG или WebP

Если файл не найден — отображается заглушка с первой буквой названия товара.

---

## Функционал

| Страница | Описание |
|---|---|
| Главная | Баннер, рекомендуемые товары, новинки, категории |
| Каталог | Фильтры по категории/бренду/цене, сортировка, пагинация |
| Карточка товара | Фото, характеристики, количество, корзина, отзывы |
| Корзина | Изменение количества, удаление, итог |
| Оформление заказа | Контакты, выбор доставки/самовывоза, комментарий |
| Личный кабинет | История заказов, редактирование профиля, смена пароля |
| Панель админа | Дашборд, товары, заказы, отзывы, клиенты |

---

## Технологии

- **Frontend:** HTML5, CSS3 (CSS Variables, Grid, Flexbox), JavaScript (vanilla)
- **Backend:** PHP 8 (PDO, Sessions, password_hash)
- **База данных:** MySQL / MariaDB (реляционная, с FK)
- **Шрифты:** IBM Plex Sans, IBM Plex Mono, Bebas Neue (Google Fonts)
- **Дизайн:** Швейцарский интернациональный типографский стиль
