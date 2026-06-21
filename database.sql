-- ============================================
-- MÖBEL — Furniture Shop Database
-- ============================================

CREATE DATABASE IF NOT EXISTS furniture_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE furniture_shop;

-- ---- CATEGORIES ----
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---- BRANDS ----
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---- PRODUCTS ----
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    category_id INT NOT NULL,
    brand_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) DEFAULT NULL,
    description TEXT,
    specs TEXT COMMENT 'JSON: {width, height, depth, material, color, weight}',
    image VARCHAR(300),
    stock INT DEFAULT 0,
    is_new TINYINT(1) DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id)
);

-- ---- USERS ----
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    role ENUM('customer','admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---- ADDRESSES ----
CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    label VARCHAR(50),
    street TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal VARCHAR(20),
    country VARCHAR(100) DEFAULT 'Россия',
    is_default TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---- ORDERS ----
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(30),
    address TEXT NOT NULL,
    city VARCHAR(100),
    postal VARCHAR(20),
    delivery_method ENUM('courier','pickup') DEFAULT 'courier',
    comment TEXT,
    status ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ---- ORDER ITEMS ----
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ---- REVIEWS ----
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    author VARCHAR(100) NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    text TEXT,
    approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- SEED DATA
-- ============================================

INSERT INTO categories (name, slug, description) VALUES
('Диваны',       'divany',    'Мягкие угловые, прямые и модульные диваны'),
('Кресла',       'kresla',    'Кресла для отдыха и чтения'),
('Столы',        'stoly',     'Обеденные, письменные и журнальные столы'),
('Стулья',       'stulya',    'Обеденные и рабочие стулья'),
('Шкафы',        'shkafy',    'Шкафы-купе, распашные и стеллажи'),
('Кровати',      'krovati',   'Двуспальные и односпальные кровати');

INSERT INTO brands (name, slug, country) VALUES
('Form & Void',   'form-void',   'Германия'),
('Nord Atelier',  'nord-atelier','Швеция'),
('Casa Semplice', 'casa-semplice','Италия'),
('Birken',        'birken',       'Финляндия');

-- ---- 20 PRODUCTS ----
INSERT INTO products (name, slug, category_id, brand_id, price, old_price, description, specs, image, stock, is_new, is_featured) VALUES

-- ДИВАНЫ (cat 1)
('Диван Stille 3-местный', 'divan-stille-3', 1, 1, 89900, 109900,
 'Минималистичный трёхместный диван с тонкими ножками из массива дуба. Съёмные чехлы, наполнитель высокой плотности.',
 '{"Ширина":"220 см","Глубина":"90 см","Высота":"75 см","Материал":"Ткань букле","Цвет":"Светло-серый","Вес":"68 кг"}',
 'stille3.jpg', 8, 0, 1),

('Диван Form угловой', 'divan-form-uglovoy', 1, 1, 134900, NULL,
 'Угловой модульный диван с независимым пружинным блоком. Правая или левая конфигурация по запросу.',
 '{"Ширина":"280×170 см","Глубина":"95 см","Высота":"80 см","Материал":"Велюр","Цвет":"Антрацит","Вес":"102 кг"}',
 'form_corner.jpg', 4, 1, 1),

('Диван Ark 2-местный', 'divan-ark-2', 1, 2, 67400, 79900,
 'Компактный двухместный диван с вертикальной прострочкой спинки. Идеален для небольших гостиных.',
 '{"Ширина":"160 см","Глубина":"82 см","Высота":"72 см","Материал":"Лён","Цвет":"Натуральный","Вес":"45 кг"}',
 'ark2.jpg', 12, 0, 0),

('Диван Folda с раскладным механизмом', 'divan-folda', 1, 3, 76500, NULL,
 'Прямой диван-кровать с механизмом «клик-кляк». Ортопедический матрас в комплекте.',
 '{"Ширина":"195 см","Глубина":"88 см","Высота":"78 см","Материал":"Экокожа","Цвет":"Молочный","Вес":"72 кг"}',
 'folda.jpg', 6, 1, 0),

-- КРЕСЛА (cat 2)
('Кресло Kivi для чтения', 'kreslo-kivi', 2, 2, 34900, NULL,
 'Эргономичное кресло с высокой спинкой и широкими подлокотниками из массива берёзы.',
 '{"Ширина":"78 см","Глубина":"80 см","Высота":"105 см","Материал":"Шерсть","Цвет":"Горчичный","Вес":"18 кг"}',
 'kivi.jpg', 15, 1, 1),

('Кресло-качалка Schaukeln', 'kreslo-kachalka-schaukeln', 2, 1, 42800, 51000,
 'Классическая качалка с дугами из гнутой фанеры. Подушка на сиденье и спинке в комплекте.',
 '{"Ширина":"65 см","Глубина":"90 см","Высота":"100 см","Материал":"Кашемир","Цвет":"Тёмно-синий","Вес":"14 кг"}',
 'schaukeln.jpg', 7, 0, 0),

('Кресло Puro офисное', 'kreslo-puro', 2, 3, 28600, NULL,
 'Рабочее кресло с поясничной поддержкой и регулировкой высоты. Сертифицировано для 8-часового использования.',
 '{"Ширина":"62 см","Глубина":"65 см","Высота":"90–105 см","Материал":"Сетка","Цвет":"Чёрный","Вес":"12 кг"}',
 'puro.jpg', 20, 0, 0),

('Кресло Lund с пуфом', 'kreslo-lund', 2, 4, 39200, 46000,
 'Уютное кресло с пуфом для ног. Скандинавский дизайн, деревянные конусные ножки.',
 '{"Ширина":"82 см","Глубина":"85 см","Высота":"83 см","Материал":"Букле","Цвет":"Кремовый","Вес":"22 кг"}',
 'lund.jpg', 9, 0, 1),

-- СТОЛЫ (cat 3)
('Стол обеденный Tafel 160', 'stol-tafel-160', 3, 1, 58900, NULL,
 'Обеденный стол с массивной столешницей из дуба и металлическими ножками в форме рогатки.',
 '{"Ширина":"160 см","Глубина":"80 см","Высота":"75 см","Материал":"Дуб / Сталь","Цвет":"Натуральный","Вес":"52 кг"}',
 'tafel160.jpg', 5, 1, 1),

('Стол письменный Grid', 'stol-grid', 3, 2, 31400, 37900,
 'Минималистичный рабочий стол с кабель-менеджментом и открытой полкой снизу.',
 '{"Ширина":"140 см","Глубина":"65 см","Высота":"73 см","Материал":"МДФ / Сталь","Цвет":"Белый матовый","Вес":"28 кг"}',
 'grid.jpg', 11, 0, 0),

('Стол журнальный Rund', 'stol-rund', 3, 4, 18700, 22000,
 'Круглый журнальный столик на деревянных ножках. Поверхность из лакированного стекла.',
 '{"Диаметр":"70 см","Высота":"42 см","Материал":"Стекло / Берёза","Цвет":"Прозрачный","Вес":"11 кг"}',
 'rund.jpg', 18, 0, 0),

('Стол обеденный Trestle раздвижной', 'stol-trestle', 3, 3, 74200, 88000,
 'Раздвижной обеденный стол на 6–10 мест. Механизм бабочки, столешница из ламинированного дуба.',
 '{"Ширина":"160–240 см","Глубина":"90 см","Высота":"75 см","Материал":"Дуб ламинат","Цвет":"Дымчатый","Вес":"78 кг"}',
 'trestle.jpg', 3, 1, 1),

-- СТУЛЬЯ (cat 4)
('Стул обеденный Knopp', 'stul-knopp', 4, 2, 12800, NULL,
 'Деревянный стул с фигурной спинкой. Массив берёзы, нескользящие накладки на ножках.',
 '{"Ширина":"46 см","Глубина":"48 см","Высота":"83 см","Материал":"Берёза","Цвет":"Белёный","Вес":"4.2 кг"}',
 'knopp.jpg', 40, 0, 0),

('Стул барный Alto', 'stul-alto', 4, 1, 16400, 19800,
 'Барный стул с регулируемой высотой. Металлическое основание, сиденье из натуральной кожи.',
 '{"Ширина":"42 см","Глубина":"45 см","Высота":"63–85 см","Материал":"Кожа / Сталь","Цвет":"Cognac","Вес":"7 кг"}',
 'alto.jpg', 24, 1, 0),

('Стул мягкий Polster', 'stul-polster', 4, 3, 22100, NULL,
 'Мягкий обеденный стул с поролоновым сиденьем и обивкой из рогожки.',
 '{"Ширина":"52 см","Глубина":"55 см","Высота":"85 см","Материал":"Рогожка / Дерево","Цвет":"Серо-зелёный","Вес":"6.5 кг"}',
 'polster.jpg', 16, 0, 1),

-- ШКАФЫ (cat 5)
('Шкаф-купе Flächig 200', 'shkaf-flachig-200', 5, 1, 96800, 118000,
 'Встроенный шкаф-купе шириной 200 см с матовыми раздвижными дверями. Внутреннее наполнение в комплекте.',
 '{"Ширина":"200 см","Глубина":"60 см","Высота":"240 см","Материал":"ЛДСП / МДФ","Цвет":"Graphite","Вес":"145 кг"}',
 'flachig200.jpg', 6, 0, 1),

('Стеллаж открытый Hylla', 'stellazh-hylla', 5, 2, 24300, NULL,
 'Открытый книжный стеллаж из берёзы. Собирается без инструментов, регулируемые полки.',
 '{"Ширина":"80 см","Глубина":"30 см","Высота":"180 см","Материал":"Берёза","Цвет":"Натуральный","Вес":"22 кг"}',
 'hylla.jpg', 14, 1, 0),

('Комод Kasten 6-ящиков', 'komod-kasten', 5, 4, 38700, 44500,
 'Деревянный комод с шестью ящиками плавного хода. Латунные ручки, основание из массива дуба.',
 '{"Ширина":"100 см","Глубина":"45 см","Высота":"90 см","Материал":"Дуб / Латунь","Цвет":"Натуральный","Вес":"48 кг"}',
 'kasten.jpg', 9, 0, 0),

-- КРОВАТИ (cat 6)
('Кровать Bett 160×200', 'krovat-bett-160', 6, 1, 112500, NULL,
 'Двуспальная кровать с изголовьем из обитого велюром каркаса. Ламельное основание в комплекте.',
 '{"Ширина":"175 см","Длина":"208 см","Высота":"90 см","Материал":"Велюр / Дерево","Цвет":"Пыльно-розовый","Вес":"62 кг"}',
 'bett160.jpg', 7, 1, 1),

('Кровать каркасная Rahmen 140×200', 'krovat-rahmen-140', 6, 4, 78400, 92000,
 'Строгий каркас из массива дуба без изголовья. Японский стиль, высота от пола 12 см.',
 '{"Ширина":"155 см","Длина":"208 см","Высота":"35 см","Материал":"Дуб","Цвет":"Мёд","Вес":"38 кг"}',
 'rahmen140.jpg', 10, 0, 1);

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@moebel.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'admin');
