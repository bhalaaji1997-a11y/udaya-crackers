CREATE DATABASE IF NOT EXISTS udaya_crackers CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE udaya_crackers;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  tamil_name VARCHAR(200) NOT NULL DEFAULT '',
  category VARCHAR(80) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  mrp DECIMAL(10,2) NOT NULL,
  unit VARCHAR(160) NOT NULL,
  image VARCHAR(120) NOT NULL,
  tag VARCHAR(60) NOT NULL DEFAULT '',
  featured TINYINT(1) NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
  low_stock_threshold INT UNSIGNED NOT NULL DEFAULT 10,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  tamil_name VARCHAR(120) NOT NULL DEFAULT '',
  description VARCHAR(180) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(24) NOT NULL UNIQUE,
  customer_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  address TEXT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status ENUM('new', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  product_name VARCHAR(160) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  line_total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO products (name, tamil_name, category, price, mrp, unit, image, tag, featured, stock_quantity, low_stock_threshold)
SELECT * FROM (
  SELECT 'Udaya Celebration Combo', 'உதயா கொண்டாட்ட காம்போ', 'Combos', 2499, 8900, '1 family box · 42 items', 'combo.jpg', 'Best value', 1, 24, 8
  UNION ALL SELECT 'Grand Family Box', 'கிராண்ட் ஃபேமிலி பாக்ஸ்', 'Combos', 4999, 16500, '1 family box · 68 items', 'combo.jpg', 'Family favourite', 1, 7, 8
  UNION ALL SELECT 'Golden Lakshmi 4"', 'கோல்டன் லட்சுமி 4"', 'Sound Crackers', 145, 520, '1 packet · 5 pieces', 'combo.jpg', '', 0, 68, 12
  UNION ALL SELECT 'Festival Thunder', 'ஃபெஸ்டிவல் தண்டர்', 'Sound Crackers', 225, 790, '1 packet · 5 pieces', 'combo.jpg', 'Popular', 1, 31, 10
  UNION ALL SELECT 'Rainbow Fountain', 'ரெயின்போ ஃபவுண்டன்', 'Fountains', 99, 360, '1 box · 3 pieces', 'fountains.jpg', '', 0, 52, 10
  UNION ALL SELECT 'Udaya Peacock Fountain', 'உதயா மயில் ஃபவுண்டன்', 'Fountains', 180, 650, '1 box · 1 piece', 'fountains.jpg', 'New', 1, 5, 8
  UNION ALL SELECT 'Colour Sparkler Mix', 'கலர் மத்தாப்பு மிக்ஸ்', 'Sparklers', 119, 420, '1 box · 10 pieces', 'sparklers.jpg', '', 0, 90, 15
  UNION ALL SELECT 'Midnight Spinner', 'மிட்நைட் ஸ்பின்னர்', 'Ground Spinners', 75, 250, '1 box · 5 pieces', 'sparklers.jpg', '', 0, 3, 8
  UNION ALL SELECT 'Sky Dazzler Rocket', 'ஸ்கை டாஸ்லர் ராக்கெட்', 'Rockets', 299, 950, '1 box · 5 pieces', 'fountains.jpg', 'Crowd pleaser', 1, 18, 6
  UNION ALL SELECT 'Star Shower', 'ஸ்டார் ஷவர்', 'Aerial Effects', 399, 1250, '1 box · 1 piece', 'fountains.jpg', '', 0, 14, 6
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM products LIMIT 1);

INSERT INTO categories (name, tamil_name, description)
SELECT * FROM (
  SELECT 'Combos', 'காம்போ பெட்டிகள்', 'Ready-to-celebrate family boxes'
  UNION ALL SELECT 'Sound Crackers', 'சத்த வெடிகள்', 'Classic celebration favourites'
  UNION ALL SELECT 'Fountains', 'தரைவாணங்கள்', 'Colourful ground fountains'
  UNION ALL SELECT 'Sparklers', 'மத்தாப்புகள்', 'Bright handheld sparklers'
  UNION ALL SELECT 'Ground Spinners', 'சக்கரங்கள்', 'Whirling ground effects'
  UNION ALL SELECT 'Rockets', 'ராக்கெட்டுகள்', 'Sky-high festival colour'
  UNION ALL SELECT 'Aerial Effects', 'வானவேடிக்கைகள்', 'Big sky moments'
) AS seed_categories
WHERE NOT EXISTS (SELECT 1 FROM categories LIMIT 1);
