

create database gaming_store;

use gaming_store ;


CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  brand VARCHAR(100) NOT NULL,
  name VARCHAR(255) NOT NULL,
  full_title VARCHAR(255),
  slug VARCHAR(255) UNIQUE,
  price DECIMAL(10,2) NOT NULL,
  currency VARCHAR(10) DEFAULT 'DH',
  status VARCHAR(50) DEFAULT 'In Stock',
  main_image VARCHAR(255) NOT NULL,
  description TEXT,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE product_gallery (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  sort_order TINYINT UNSIGNED DEFAULT 0,
  FOREIGN KEY (product_id)
  REFERENCES products(id)
  ON DELETE CASCADE
);


INSERT INTO products (id, brand, name, full_title, slug, price, currency, status, main_image, description) VALUES
(1, 'SKILLCHAIRS', 'Aurelia Series', 'SKILLCHAIRS Aurelia', 'skillchairs-aurelia-series', 1499.00, 'DH', 'In Stock', 'chair-1.webp', 'The SKILLCHAIRS Aurelia Morocco stands out for its ability to offer superior comfort while bringing a touch of rare elegance to the world of gaming and office chairs . Designed to meet the expectations of users looking for a modern, comfortable, and durable ergonomic chair, it has become a benchmark for gamers, remote workers, and professionals in Morocco.'),
(2, 'RAZER', 'Razer Iskur V2 X Black', 'SKILLCHAIRS Aurelia', 'razer-iskur-v2-x-black-1', 3499.00, 'DH', 'In Stock', 'chair-3.webp', 'The Razer Iskur V2 X Black Morocco perfectly embodies the Razer brand spirit, combining elegance and functionality. Its timeless black color gives it a sleek and professional look, equally at home in a gaming setup or a modern workspace. Built with a reinforced steel frame and high-quality materials, this chair is designed for durability, even with intensive use. Thanks to its ergonomic design and durable upholstery, the Iskur V2 X Black offers comfortable seating while ensuring optimal stability for long gaming or work sessions.'),
(3, 'RAZER', 'Razer Iskur V2 X Black', 'SKILLCHAIRS Aurelia', 'razer-iskur-v2-x-black-2', 3499.00, 'DH', 'In Stock', 'chair-5.webp', 'The Razer Iskur V2 X Black Morocco perfectly embodies the Razer brand spirit, combining elegance and functionality. Its timeless black color gives it a sleek and professional look, equally at home in a gaming setup or a modern workspace. Built with a reinforced steel frame and high-quality materials, this chair is designed for durability, even with intensive use. Thanks to its ergonomic design and durable upholstery, the Iskur V2 X Black offers comfortable seating while ensuring optimal stability for long gaming or work sessions.'),
(4, 'RAZER', 'Hybrok Destinity - The gaming chair designed for performance and long-lasting comfort', 'SKILLCHAIRS Aurelia', 'hybrok-destinity-gaming-chair', 2199.00, 'DH', 'In Stock', 'chair-7.webp', 'The Hybrok Destinity Morocco stands out as a modern gaming chair designed to meet the needs of demanding gamers, streamers, and professionals who spend long hours in front of their setups. Combining a bold design, advanced ergonomics, and robust materials, it offers a balanced seating experience that combines comfort, support, and style, while seamlessly integrating into a high-end gaming or office environment.'),
(5, 'HYBROK', 'Hybrok Fighter: the gaming chair designed for intensity and control', 'SKILLCHAIRS Aurelia', 'hybrok-fighter-gaming-chair', 2199.00, 'DH', 'In Stock', 'chair_9.webp', 'The Hybrok Fighter Morocco is designed for passionate gamers and demanding users who want a gaming chair capable of keeping up with even the most intense gaming sessions. Inspired by the world of competitive gaming and esports, it combines a bold design, carefully considered ergonomics, and a robust build. Designed for long-lasting comfort, the Fighter transforms every hour spent in front of the screen into a more stable, immersive, and high-performance experience.'),
(6, 'SKILLDESK', 'SKILLDESK NEXUS', 'SKILLCHAIRS Aurelia', 'skilldesk-nexus', 999.00, 'DH', 'In Stock', 'desk-1.webp', 'The SKILLDESK NEXUS Morocco is more than just a desk; it\"s a complete workstation designed to meet the needs of demanding gamers, content creators, and professionals. Combining futuristic design, robustness, and intelligent features, it redefines how you interact with your workspace or gaming environment. With its premium design and durable materials, the NEXUS is the ideal choice for anyone seeking ergonomics, productivity, and long-term comfort.'),
(7, 'SKILLDESK', 'SKILLDESK WARZONE 140', 'SKILLDESK WARZONE 140: The ultimate gaming desk for a powerful and immersive setup', 'skilldesk-warzone-140', 1099.00, 'DH', 'In Stock', 'desk-3.webp', 'The SKILLDESK WARZONE 140 Morocco is designed to meet the demands of modern gamers, streamers, and professionals looking for a spacious, robust, and stylish manually adjustable gaming desk. Built to accommodate even the most ambitious setups, this gaming desk combines sturdiness, ergonomics, and premium design to deliver an exceptional gaming and work experience. Thanks to its optimized design, it stands out as an ideal solution for creating a high-performance and organized workspace.'),
(8, 'SKILLDESK', 'SKILLDESK BLACK FLAG (Motorisé)', 'SKILLDESK BLACK FLAG (Motorized): High-Performance and Stylish Gaming Desk', 'skilldesk-black-flag-motorise', 1999.00, 'DH', 'In Stock', 'desk-5.webp', 'The SKILLDESK BLACK FLAG (Motorized) Morocco is a gaming desk designed for video game enthusiasts and professionals seeking an optimized workstation. This desk combines ergonomics, robustness, and modern design, creating a gaming space perfectly suited to the needs of Moroccan gamers. Whether you\"re a casual player or a seasoned competitor, the SKILLDESK BLACK FLAG offers all the necessary features to enhance your gaming experience.'),
(9, 'SKILLDESK', 'SKILLDESK COCKPIT 180', 'SKILLDESK COCKPIT 180: The gaming desk designed for an immersive and high-performance setup', 'skilldesk-cockpit-180', 1599.00, 'DH', 'In Stock', 'desk-7.webp', 'The SKILLDESK COCKPIT 180 Morocco is a high-end gaming desk designed for passionate gamers, streamers, and professionals who demand a spacious, robust, and perfectly optimized workspace. With its size, solid construction, and design inspired by gaming cockpits, this gaming desk offers an ideal platform for powerful setups..'),
(10, 'PLAYER-AZ1', '1st Player AZ1 table gaming', '1st Player AZ1 Table Gaming: Ergonomics and Performance for Gamers in Morocco', '1st-player-az1-table-gaming', 3199.00, 'DH', 'In Stock', 'desk-10.webp', 'The 1st Player AZ1 Table Gaming is designed to meet the needs of the most demanding players in Morocco. This gaming desktop combines ergonomic design, robustness and features adapted to long gaming sessions, thus providing an optimal user experience. Whether you are a professional player or a passionate amateur, this gaming table turns your space into an efficient and stylish gaming environment.'),
(11, 'GAMESIR', 'GameSir T4 Pro Wireless Controller (Black)', 'GameSir T4 Pro Wireless Controller (Black) – The ultimate controller for cross-platform gamers', 'gamesir-t4-pro-wireless-controller-black', 499.00, 'DH', 'In Stock', 'Controller-5.webp', 'At a time when video games are practiced on several media, the GameSir T4 Pro Wireless Controller (Black) is a must-have solution. Whether you’re a player on PC, Android or iOS, this wireless controller is designed to fit all your needs. With its transparent black backlit design RGB, its 6-axis gyroscope, its asymmetrical vibrations, and its configurable Turbo mode, it is quickly becoming an essential for those who want to take it to the next level.'),
(12, 'GAMESIR', 'GameSir G8 Galileo Type-C Mobile Gaming Controller', 'GameSir G8 Galileo Type-C Mobile Gaming Controller : le futur du gaming mobile est en marche.', 'gamesir-g8-galileo-type-c-mobile', 899.00, 'DH', 'In Stock', 'Controller-7.webp', 'In a world where mobile gaming is growing, the GameSir G8 Galileo Type-C Mobile Gaming Controller is establishing itself as a technological revolution designed for the most demanding players. Featuring console ergonomics, universal compatibility with Android smartphones in USB-C, and smart modular design, this controller turns your phone into a true portable console. Whether you’re a fan of competitive FPS, solo adventures or immersive racing games, the GameSir G8 Galileo is redefining next-generation mobile gaming standards.'),
(13, 'GAMESIR', 'GameSir SuperNova T4n Pro Wireless Controller (Rose)', 'GameSir SuperNova T4n Pro Wireless Controller (Rose): the stylish and powerful wireless gaming controller in Morocco', 'gamesir-supernova-t4n-pro-rose', 549.00, 'DH', 'In Stock', 'Controller-9.webp', 'The GameSir SuperNova T4n Pro Wireless Controller (Rose) Morocco is a gaming controller that perfectly combines modern design, advanced performance and comfort of use. With its elegant and trendy pink color, it is aimed at gamers in Morocco who are looking for both style and efficiency. This wireless controller ensures a smooth, accurate and immersive experience.'),
(14, 'NINTEND', 'Nintendo GLOW Wired Controller & Travel Case Bundle', 'Bundle Nintendo GLOW wired controller & travel case: a perfect combo for gamers in Morocco', 'nintendo-glow-wired-controller-bundle', 549.00, 'DH', 'In Stock', 'Controller-11.webp', 'The Nintendo GLOW Wired Controller & Travel Case Bundle is the perfect choice for players who are looking for style, performance and convenience. With its bright and ergonomic design, this wired controller offers an immersive gaming experience, while its robust travel case allows it to be transported easily and safely. Available in Morocco, this bundle is designed for Nintendo Switch enthusiasts.'),
(15, 'SONY', 'Manette Sony DualSense V2 PS5 (Blanc)', 'Explore the Sony DualSense V2 white controller in Morocco', 'manette-sony-dualsense-v2-ps5-blanc', 949.00, 'DH', 'In Stock', 'Controller-13.webp', 'The Sony DualSense V2 White controller revolutionizes the gaming experience with its innovative design and advanced features. Designed for demanding players, this controller offers unprecedented immersion thanks to its haptic capabilities and adaptive tactile feedback, available exclusively in Morocco for PlayStation enthusiasts.'),
(16, 'SONY', 'Sony DualSense Controller (Blue Cobalt)', 'Explore the Sony DualSense Cobalt Blue controller for an innovative gaming experience in Morocco', 'sony-dualsense-controller-blue-cobalt', 899.00, 'DH', 'In Stock', 'Controller-15.webp', 'Ergonomic comfort for extended play sessions The Blue PS5 DualSense Cobalt Controller is designed with attention to detail to provide exceptional ergonomic comfort, allowing you to play for hours without fatigue.'),
(17, 'SONY', 'Sony PlayStation 5 Slim Digital Edition Fortnite Cobalt Star Bundle', 'Sony PlayStation 5 Slim Digital Edition Fortnite Cobalt Star Bundle: The ultimate gaming experience in Morocco', 'sony-playstation-5-slim-fortnite-bundle', 5490.00, 'DH', 'In Stock', 'PlayStation-1.webp', 'Discover the NouveautéSony PlayStation 5 Slim Digital Edition Fortnite Cobalt Star Bundle, a compact and powerful version of the console, with exclusive Fortnite content. Whether you are a fan of high-performance gaming or relaxing parties, this pack is designed to revolutionize your gaming experience.'),
(18, 'SONY', 'Sony PlayStation 5 Slim + 2nd Controller DualSense', 'Sony PlayStation 5 Slim + 2nd Controller DualSense - The Next-Gen Experience to Share', 'sony-playstation-5-slim-2nd-controller', 6999.00, 'DH', 'In Stock', 'PlayStation-3.webp', 'The Sony PlayStation 5 Slim Morocco is the stylish and modern answer to anyone looking for the power of the PS5 in a thinner, lighter, and more modern interior format. Without sacrificing the performance of the original console, this Slim version embodies a smart technological evolution, designed to fit perfectly into your living room. With this exclusive pack featuring a second DualSense controller, you’re ready to dive into the immersive world of next-gen. Whether for FIFA matches, battles in Mortal Kombat or cooperative sessions on It Takes Two, this offer is ideal for families, flatshares or local multiplayer enthusiasts.'),
(19, 'SONY', 'Sony PlayStation 4 Slim (500 Go)', 'Sony PlayStation 4 Slim (500 GB) in Morocco: The ultimate game console for everyone', 'sony-playstation-4-slim-500-go', 3299.00, 'DH', 'In Stock', 'PlayStation-5.webp', 'The Sony PlayStation 4 Slim (500 GB) is an iconic gaming console that continues to captivate video game enthusiasts from around the world. With its elegant design, exceptional performance and extensive gaming catalog, it is an ideal choice for gamers in Morocco. Discover why the PS4 Slim remains an essential reference.'),
(20, 'SONY', 'Sony PlayStation 5 Slim Digital Edition + 2nd controller DualSense', 'Discover the Sony PlayStation 5 Slim Digital Edition with a 2nd DualSense controller in Morocco', 'sony-playstation-5-slim-digital-2nd-controller', 6599.00, 'DH', 'In Stock', 'PlayStation-7.webp', 'The Sony PlayStation 5 Slim Digital Edition represents a major breakthrough in the world of game consoles. Offering Moroccan players an immersive experience with breathtaking graphics and improved performance, this console is intended to redefine video games. Accompanied by a second DualSense controller, it promises unforgettable shared game moments.'),
(21, 'LOGITECH', 'Logitech G502 Hero RGB', 'Explore accuracy with the Logitech G502 Hero RGB in Morocco', 'logitech-g502-hero-rgb', 449.00, 'DH', 'In Stock', 'Souris-4.webp', 'Immerse yourself in the world of advanced gaming with the Logitech G502 Hero RGB gaming mouse, a choice popular with Moroccan gamers for its unparalleled precision and customizable design..'),
(22, 'RAZER', 'Razer Basilisk v3', 'Mouse Razer Basilisk V3 Morocco – Absolute control, performance and comfort', 'razer-basilisk-v3', 449.00, 'DH', 'In Stock', 'Souris-6.webp', 'The Souris Razer Basilisk V3 Morocco is an ideal solution for Moroccan gamers looking for precision, ergonomics and customization. Featuring a Focus+ optical sensor of 26,000 DPI, 11 programmable buttons and immersive 11-zone RGB lighting, this wired mouse transforms the way you play. In other words, it combines power and comfort for total domination.'),
(23, 'REDRAGON', 'Redragon M916AK PRO 1K Wireless', 'Redragon M916AK PRO 1K Wireless: the next-generation wireless gaming mouse', 'redragon-m916ak-pro-1k-wireless', 319.00, 'DH', 'In Stock', 'Souris-8.webp', 'The Redragon M916AK PRO Morocco is an essential choice for players looking for a perfect balance between performance and freedom. Thanks to its ultra-fast 1K wireless connectivity, this mouse offers a smooth and responsive gaming experience, without the slightest latency. Its ergonomic design naturally matches the shape of the hand, guaranteeing optimal comfort even during long, intense play sessions. In addition to its robustness, its lightweight but strong chassis allows to maintain excellent maneuverability, ideal for competitive games where every millisecond counts.'),
(24, 'LOGITECH', 'Logitech G G502X White', 'Logitech G G502X White Morocco: The Power of Gaming in a Stylish Design', 'logitech-g-g502x-white', 569.00, 'DH', 'In Stock', 'Souris-10.webp', 'The Logitech G G502X White impresses with its elegant and ergonomic design. Rubber side grips ensure a comfortable grip, while programmable buttons offer total customization to fit your playing style.'),
(25, 'XTRMLAB', 'XTRMLAB X27G14IFF 27\' 144Hz IPS', 'XTRMLAB X27G14IFF 27\' 144Hz IPS - Fluidity and precision on large screen', 'xtrmlab-x27g14iff-27-144hz-ips', 999.00, 'DH', 'In Stock', 'ecarn-1.webp', 'The XTRMLAB X27G14IFF 27\' 144Hz IPS Morocco embodies the perfect combination of visual performance, extreme fluidity and modern design. Designed to deliver an immersive gaming and work experience, this 27-inch monitor delivers clear, accurate and seamless images with 144 Hz refresh rates and IPS Full HD slab.'),
(26, 'XTRMLAB', 'XTRMLAB XK25G24IFF 24.5\' 240Hz Fast IPS', 'XTRMLAB XK25G24IFF 24.5\' 240Hz Fast IPS - The ultra-reactive monitor designed for competitive players', 'xtrmlab-xk25g24iff-24-5-240hz-fast-ips', 1099.00, 'DH', 'In Stock', 'ecarn-3.webp', 'The XTRMLAB XK25G24IFF 24.5\' 240Hz Fast IPS Morocco is for players looking for extreme fluidity, optimal visual accuracy and uncompromising e-sports experience. Designed to meet the requirements of competitive players, this monitor combines a Fast IPS panel, a 240 Hz refresh rate, ultra-fast response time and faithful color reproduction.'),
(27, 'MSI', 'MSI G255F 24.5\' Rapid IPS 180Hz', 'MSI G255F 24.5\' Rapid IPS 180Hz: an immersive gaming experience in Morocco', 'msi-g255f-24-5-rapid-ips-180hz', 1599.00, 'DH', 'In Stock', 'ecran-5.webp', 'The MSI G255F 24.5\' Rapid IPS 180Hz is more than just a gaming screen. Designed for gamers looking for smooth performance and outstanding image quality, this screen is the perfect ally to dominate every game session. With its fast IPS slab, an impressive 180Hz refresh rate and ultra-fast response time, it meets the requirements of the most competitive players.'),
(28, 'AOC', 'AOC C27G4H 27\' 180Hz 0.5ms FHD Curved', 'AOC C27G4H 27\' 180Hz 0.5ms FHD – An immersive monitor for gamers who make no compromises', 'aoc-c27g4h-27-180hz-0-5ms-fhd-curved', 1549.00, 'DH', 'In Stock', 'ecran-7.webp', 'The AOC C27G4H 27\' 180Hz 0.5ms FHD Morocco redefines immersive gaming standards with its 27-inch , 180 Hz frequency and a super-fast response time of 0.5 ms. Designed for demanding players looking for a competitive advantage and superior visual comfort, this AOC-signed FHD AOC monitor combines performance, style and ergonomics at an affordable price.');


select * from products;

INSERT INTO product_gallery (product_id, image_path, sort_order) VALUES

(1, 'chair-2.webp', 1),
(1, 'shop_chair1.webp', 2),
(1, 'shop_chair2.webp', 3),
(1, 'shop_chair3.webp', 4),

(2, 'chair-4.webp', 1),
(2, 'shop_chair4.webp', 2),
(2, 'shop_chair5.webp', 3),

(3, 'chair-6.webp', 1),
(3, 'shop_chair6.webp', 2),
(3, 'shop_chair7.webp', 3),
-- Product 4
(4, 'chair-8.webp', 1),
(4, 'shop_chair8.webp', 2),
(4, 'shop_chair9.webp', 3),

(5, 'chair-10.webp', 1),
(5, 'shop_chair10.webp', 2),
(5, 'shop_chair11.webp', 3),

(6, 'desk-2.webp', 1),
(6, 'shop_desk1.webp', 2),
(6, 'shop_desk2.webp', 3),

(7, 'desk-4.webp', 1),
(7, 'shop_desk3.webp', 2),
(7, 'shop_desk4.webp', 3),

(8, 'desk-6.webp', 1),
(8, 'shop_desk6webp.webp', 2),
(8, 'shop_desk7webp.webp', 3),

(9, 'desk-8.webp', 1),
(9, 'shop_desk8webp.webp', 2),

(10, 'desk-11.webp', 1),
(10, 'shop_desk9.webp', 2),
(10, 'shop_desk10.webp', 3),

(11, 'Controller-6.webp', 1),
(11, 'shop_desk11.webp', 2),

(12, 'Controller-8.webp', 1),
(12, 'shop_desk12.webp', 2),
(12, 'shop_desk13.webp', 3),
-- Product 13
(13, 'Controller-10.webp', 1),
(13, 'shop_desk14.webp', 2),
-- Product 14
(14, 'Controller-12.webp', 1),
(14, 'shop_desk15.webp', 2),
-- Product 15
(15, 'Controller-14.webp', 1),
(15, 'shop_desk16.webp', 2),
-- Product 16
(16, 'Controller-16.webp', 1),
(16, 'shop_desk17.webp', 2),
-- Product 17
(17, 'PlayStation-2.webp', 1),
-- Product 18
(18, 'PlayStation-4.webp', 1),
-- Product 19
(19, 'PlayStation-6.webp', 1),
-- Product 20
(20, 'PlayStation-8.webp', 1),
(20, 'shop_desk18.webp', 2),
-- Product 21
(21, 'Souris-5.webp', 1),
(21, 'shop_desk19.webp', 2),
-- Product 22
(22, 'Souris-7.webp', 1),
(22, 'shop_desk20.webp', 2),
-- Product 23
(23, 'Souris-9.webp', 1),
(23, 'shop_desk21.webp', 2),
-- Product 24
(24, 'Souris-11webp.webp', 1),
(24, 'shop_desk22.webp', 2),
-- Product 25
(25, 'ecarn-2.webp', 1),
(25, 'shop_desk23.webp', 2),
(25, 'shop_desk24.webp', 3),
-- Product 26
(26, 'ecran-4.webp', 1),
(26, 'shop_desk25.webp', 2),
-- Product 27
(27, 'ecran-6.webp', 1),
(27, 'shop_desk26.webp', 2),
-- Product 28
(28, 'ecran-8.webp', 1),
(28, 'shop_desk27.webp', 2);

select * from product_gallery ;


SELECT p.*, g.image_path AS hover_image 
            FROM products p
            LEFT JOIN product_gallery g ON p.id = g.product_id AND g.sort_order = 0
            WHERE p.is_active = 1 
            ORDER BY p.id ASC;


