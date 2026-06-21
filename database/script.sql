-- ============================================================
-- TECH SHOP — Fixed & Complete Database Script
-- Run this from scratch (drop old DB first if it exists)
-- ============================================================

DROP DATABASE IF EXISTS gaming_store;
CREATE DATABASE gaming_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gaming_store;


CREATE TABLE products (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category    VARCHAR(50)  NOT NULL,                    
  brand       VARCHAR(100) NOT NULL,
  name        VARCHAR(255) NOT NULL,
  full_title  VARCHAR(255),
  slug        VARCHAR(255)  UNIQUE,
  price       DECIMAL(10,2) NOT NULL,
  currency    VARCHAR(10)   DEFAULT 'DH',
  status      VARCHAR(50)   DEFAULT 'In Stock',
  main_image  VARCHAR(255)  NOT NULL,
  description TEXT,
  is_active   TINYINT(1) DEFAULT 1,
  created_at  TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_category   (category),
  INDEX idx_active     (is_active),
  INDEX idx_cat_active (category, is_active)
);


CREATE TABLE product_gallery (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id  INT UNSIGNED NOT NULL,
  image_path  VARCHAR(255) NOT NULL,
  image_type  ENUM('home','shop','extra') NOT NULL DEFAULT 'home',
  sort_order  TINYINT UNSIGNED DEFAULT 0,

  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_product_type (product_id, image_type)
);


INSERT INTO products (id, category, brand, name, full_title, slug, price, currency, status, main_image, description) VALUES

-- CHAIRS (category = 'chair')
(1,  'chair', 'SKILLCHAIRS', 'Aurelia Series', 'SKILLCHAIRS Aurelia', 'skillchairs-aurelia-series', 1499.00, 'DH', 'In Stock', 'chair-1.webp', 'The SKILLCHAIRS Aurelia Morocco stands out for its ability to offer superior comfort while bringing a touch of rare elegance to the world of gaming and office chairs.'),
(2,  'chair', 'RAZER', 'Razer Iskur V2 X Black', 'RAZER Iskur V2 X Black', 'razer-iskur-v2-x-black-1', 3499.00, 'DH', 'In Stock', 'chair-3.webp', 'The Razer Iskur V2 X Black Morocco perfectly embodies the Razer brand spirit, combining elegance and functionality.'),
(3,  'chair', 'RAZER', 'Razer Iskur V2 X Green', 'RAZER Iskur V2 X Green', 'razer-iskur-v2-x-black-2', 3499.00, 'DH', 'In Stock', 'chair-5.webp', 'The Razer Iskur V2 X Morocco perfectly embodies the Razer brand spirit, combining elegance and functionality.'),
(4,  'chair', 'HYBROK', 'Hybrok Destinity', 'Hybrok Destinity Gaming Chair', 'hybrok-destinity-gaming-chair', 2199.00, 'DH', 'In Stock', 'chair-7.webp', 'The Hybrok Destinity Morocco stands out as a modern gaming chair designed to meet the needs of demanding gamers.'),
(5,  'chair', 'HYBROK', 'Hybrok Fighter', 'Hybrok Fighter Gaming Chair', 'hybrok-fighter-gaming-chair', 2199.00, 'DH', 'In Stock', 'chair_9.webp', 'The Hybrok Fighter Morocco is designed for passionate gamers and demanding users.'),

-- DESKS (category = 'desk')
(6,  'desk', 'SKILLDESK', 'SKILLDESK NEXUS', 'SKILLDESK NEXUS', 'skilldesk-nexus', 999.00, 'DH', 'In Stock', 'desk-1.webp', 'The SKILLDESK NEXUS Morocco is more than just a desk.'),
(7,  'desk', 'SKILLDESK', 'SKILLDESK WARZONE 140', 'SKILLDESK WARZONE 140', 'skilldesk-warzone-140', 1099.00, 'DH', 'In Stock', 'desk-3.webp', 'The SKILLDESK WARZONE 140 Morocco is designed to meet the demands of modern gamers.'),
(8,  'desk', 'SKILLDESK', 'SKILLDESK BLACK FLAG (Motorisé)', 'SKILLDESK BLACK FLAG Motorized', 'skilldesk-black-flag-motorise', 1999.00, 'DH', 'In Stock', 'desk-5.webp', 'The SKILLDESK BLACK FLAG (Motorized) Morocco is a gaming desk designed for video game enthusiasts.'),
(9,  'desk', 'SKILLDESK', 'SKILLDESK COCKPIT 180', 'SKILLDESK COCKPIT 180', 'skilldesk-cockpit-180', 1599.00, 'DH', 'In Stock', 'desk-7.webp', 'The SKILLDESK COCKPIT 180 Morocco is a high-end gaming desk.'),
(10, 'desk', 'PLAYER-AZ1', '1st Player AZ1 table gaming', '1st Player AZ1 Table Gaming', '1st-player-az1-table-gaming', 3199.00, 'DH', 'In Stock', 'desk-10.webp', 'The 1st Player AZ1 Table Gaming is designed to meet the needs of the most demanding players.'),

-- CONTROLLERS (category = 'controller')
(11, 'controller', 'GAMESIR', 'GameSir T4 Pro Wireless Controller (Black)', 'GameSir T4 Pro Wireless Controller Black', 'gamesir-t4-pro-wireless-controller-black', 499.00, 'DH', 'In Stock', 'Controller-5.webp', 'The GameSir T4 Pro Wireless Controller (Black) is a must-have solution.'),
(12, 'controller', 'GAMESIR', 'GameSir G8 Galileo Type-C', 'GameSir G8 Galileo Type-C Mobile Gaming Controller', 'gamesir-g8-galileo-type-c-mobile', 899.00, 'DH', 'In Stock', 'Controller-7.webp', 'The GameSir G8 Galileo Type-C Mobile Gaming Controller is establishing itself as a technological revolution.'),
(13, 'controller', 'GAMESIR', 'GameSir SuperNova T4n Pro (Rose)', 'GameSir SuperNova T4n Pro Wireless Controller Rose', 'gamesir-supernova-t4n-pro-rose', 549.00, 'DH', 'In Stock', 'Controller-9.webp', 'The GameSir SuperNova T4n Pro Wireless Controller (Rose) Morocco perfectly combines modern design.'),
(14, 'controller', 'NINTEND', 'Nintendo GLOW Wired Controller & Bundle', 'Nintendo GLOW Wired Controller and Travel Case Bundle', 'nintendo-glow-wired-controller-bundle', 549.00, 'DH', 'In Stock', 'Controller-11.webp', 'The Nintendo GLOW Wired Controller & Travel Case Bundle is the perfect choice for players.'),
(15, 'controller', 'SONY', 'Manette Sony DualSense V2 PS5 (Blanc)', 'Sony DualSense V2 White', 'manette-sony-dualsense-v2-ps5-blanc', 949.00, 'DH', 'In Stock', 'Controller-13.webp', 'The Sony DualSense V2 White controller revolutionizes the gaming experience.'),
(16, 'controller', 'SONY', 'Sony DualSense Controller (Blue Cobalt)', 'Sony DualSense Cobalt Blue', 'sony-dualsense-controller-blue-cobalt', 899.00, 'DH', 'In Stock', 'Controller-15.webp', 'Ergonomic comfort for extended play sessions.'),

-- PLAYSTATION (category = 'playstation')
(17, 'playstation', 'SONY', 'Sony PlayStation 5 Slim Digital Edition Fortnite Bundle', 'Sony PS5 Slim Fortnite Bundle', 'sony-playstation-5-slim-fortnite-bundle', 5490.00, 'DH', 'In Stock', 'PlayStation-1.webp', 'Discover the Sony PlayStation 5 Slim Digital Edition Fortnite Cobalt Star Bundle.'),
(18, 'playstation', 'SONY', 'Sony PlayStation 5 Slim + 2nd Controller DualSense', 'Sony PS5 Slim 2nd Controller', 'sony-playstation-5-slim-2nd-controller', 6999.00, 'DH', 'In Stock', 'PlayStation-3.webp', 'The Sony PlayStation 5 Slim Morocco is the stylish and modern answer.'),
(19, 'playstation', 'SONY', 'Sony PlayStation 4 Slim (500 Go)', 'Sony PS4 Slim 500GB', 'sony-playstation-4-slim-500-go', 3299.00, 'DH', 'In Stock', 'PlayStation-5.webp', 'The Sony PlayStation 4 Slim (500 GB) is an iconic gaming console.'),
(20, 'playstation', 'SONY', 'Sony PlayStation 5 Slim Digital Edition + 2nd DualSense', 'Sony PS5 Slim Digital 2nd Controller', 'sony-playstation-5-slim-digital-2nd-controller', 6599.00, 'DH', 'In Stock', 'PlayStation-7.webp', 'The Sony PlayStation 5 Slim Digital Edition represents a major breakthrough.'),

-- MOUSE (category = 'mouse')
(21, 'mouse', 'LOGITECH', 'Logitech G502 Hero RGB', 'Logitech G502 Hero RGB', 'logitech-g502-hero-rgb', 449.00, 'DH', 'In Stock', 'Souris-4.webp', 'Immerse yourself in the world of advanced gaming with the Logitech G502 Hero RGB.'),
(22, 'mouse', 'RAZER', 'Razer Basilisk v3', 'Razer Basilisk V3', 'razer-basilisk-v3', 449.00, 'DH', 'In Stock', 'Souris-6.webp', 'The Razer Basilisk V3 Morocco is an ideal solution for Moroccan gamers.'),
(23, 'mouse', 'REDRAGON', 'Redragon M916AK PRO 1K Wireless', 'Redragon M916AK PRO 1K Wireless', 'redragon-m916ak-pro-1k-wireless', 319.00, 'DH', 'In Stock', 'Souris-8.webp', 'The Redragon M916AK PRO Morocco is an essential choice for players.'),
(24, 'mouse', 'LOGITECH', 'Logitech G G502X White', 'Logitech G502X White', 'logitech-g-g502x-white', 569.00, 'DH', 'In Stock', 'Souris-10.webp', 'The Logitech G G502X White impresses with its elegant and ergonomic design.'),

-- ECRAN / MONITORS (category = 'ecran')
(25, 'ecran', 'XTRMLAB', "XTRMLAB X27G14IFF 27' 144Hz IPS", "XTRMLAB X27G14IFF 27inch 144Hz IPS", 'xtrmlab-x27g14iff-27-144hz-ips', 999.00, 'DH', 'In Stock', 'ecarn-1.webp', 'The XTRMLAB X27G14IFF 144Hz IPS Morocco embodies the perfect combination of visual performance.'),
(26, 'ecran', 'XTRMLAB', "XTRMLAB XK25G24IFF 24.5' 240Hz Fast IPS", "XTRMLAB XK25G24IFF 24.5inch 240Hz", 'xtrmlab-xk25g24iff-24-5-240hz-fast-ips', 1099.00, 'DH', 'In Stock', 'ecarn-3.webp', 'The XTRMLAB XK25G24IFF 240Hz Fast IPS Morocco is for players looking for extreme fluidity.'),
(27, 'ecran', 'MSI', "MSI G255F 24.5' Rapid IPS 180Hz", "MSI G255F 24.5inch Rapid IPS 180Hz", 'msi-g255f-24-5-rapid-ips-180hz', 1599.00, 'DH', 'In Stock', 'ecran-5.webp', 'The MSI G255F 24.5 Rapid IPS 180Hz is more than just a gaming screen.'),
(28, 'ecran', 'AOC', "AOC C27G4H 27' 180Hz 0.5ms FHD Curved", "AOC C27G4H 27inch 180Hz Curved", 'aoc-c27g4h-27-180hz-0-5ms-fhd-curved', 1549.00, 'DH', 'In Stock', 'ecran-7.webp', 'The AOC C27G4H 27 180Hz 0.5ms FHD Morocco redefines immersive gaming standards.');



INSERT INTO product_gallery (product_id, image_path, image_type, sort_order) VALUES

-- Product 1 (chair)
(1, 'chair-2.webp',    'home', 1),
(1, 'shop_chair1.webp','shop', 1),
(1, 'shop_chair2.webp','shop', 2),
(1, 'shop_chair3.webp','shop', 3),

-- Product 2 (chair)
(2, 'chair-4.webp',    'home', 1),
(2, 'shop_chair4.webp','shop', 1),
(2, 'shop_chair5.webp','shop', 2),

-- Product 3 (chair)
(3, 'chair-6.webp',    'home', 1),
(3, 'shop_chair6.webp','shop', 1),
(3, 'shop_chair7.webp','shop', 2),

-- Product 4 (chair)
(4, 'chair-8.webp',    'home', 1),
(4, 'shop_chair8.webp','shop', 1),
(4, 'shop_chair9.webp','shop', 2),

-- Product 5 (chair)
(5, 'chair-10.webp',    'home', 1),
(5, 'shop_chair10.webp','shop', 1),
(5, 'shop_chair11.webp','shop', 2),

-- Product 6 (desk)
(6, 'desk-2.webp',    'home', 1),
(6, 'shop_desk1.webp','shop', 1),
(6, 'shop_desk2.webp','shop', 2),

-- Product 7 (desk)
(7, 'desk-4.webp',    'home', 1),
(7, 'shop_desk3.webp','shop', 1),
(7, 'shop_desk4.webp','shop', 2),

-- Product 8 (desk)
(8, 'desk-6.webp',        'home', 1),
(8, 'shop_desk6webp.webp','shop', 1),
(8, 'shop_desk7webp.webp','shop', 2),

-- Product 9 (desk)
(9, 'desk-8.webp',        'home', 1),
(9, 'shop_desk8webp.webp','shop', 1),

-- Product 10 (desk)
(10, 'desk-11.webp',   'home', 1),
(10, 'shop_desk9.webp','shop', 1),
(10, 'shop_desk10.webp','shop',2),

-- Product 11 (controller)
(11, 'Controller-6.webp', 'home', 1),
(11, 'shop_desk11.webp',  'shop', 1),

-- Product 12 (controller)
(12, 'Controller-8.webp', 'home', 1),
(12, 'shop_desk12.webp',  'shop', 1),
(12, 'shop_desk13.webp',  'shop', 2),

-- Product 13 (controller)
(13, 'Controller-10.webp','home', 1),
(13, 'shop_desk14.webp',  'shop', 1),

-- Product 14 (controller)
(14, 'Controller-12.webp','home', 1),
(14, 'shop_desk15.webp',  'shop', 1),

-- Product 15 (controller)
(15, 'Controller-14.webp','home', 1),
(15, 'shop_desk16.webp',  'shop', 1),

-- Product 16 (controller)
(16, 'Controller-16.webp','home', 1),
(16, 'shop_desk17.webp',  'shop', 1),

-- Product 17 (playstation)
(17, 'PlayStation-2.webp','home', 1),

-- Product 18 (playstation)
(18, 'PlayStation-4.webp','home', 1),

-- Product 19 (playstation)
(19, 'PlayStation-6.webp','home', 1),

-- Product 20 (playstation)
(20, 'PlayStation-8.webp','home', 1),
(20, 'shop_desk18.webp',  'shop', 1),

-- Product 21 (mouse)
(21, 'Souris-5.webp',  'home', 1),
(21, 'shop_desk19.webp','shop', 1),

-- Product 22 (mouse)
(22, 'Souris-7.webp',  'home', 1),
(22, 'shop_desk20.webp','shop', 1),

-- Product 23 (mouse)
(23, 'Souris-9.webp',  'home', 1),
(23, 'shop_desk21.webp','shop', 1),

-- Product 24 (mouse)
(24, 'Souris-11webp.webp','home', 1),
(24, 'shop_desk22.webp',  'shop', 1),

-- Product 25 (ecran)
(25, 'ecarn-2.webp',   'home', 1),
(25, 'shop_desk23.webp','shop', 1),
(25, 'shop_desk24.webp','shop', 2),

-- Product 26 (ecran)
(26, 'ecran-4.webp',   'home', 1),
(26, 'shop_desk25.webp','shop', 1),

-- Product 27 (ecran)
(27, 'ecran-6.webp',   'home', 1),
(27, 'shop_desk26.webp','shop', 1),

-- Product 28 (ecran)
(28, 'ecran-8.webp',   'home', 1),
(28, 'shop_desk27.webp','shop', 1);


