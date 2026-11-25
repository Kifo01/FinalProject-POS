-- FinalProject POS local setup. Import with phpMyAdmin.
CREATE DATABASE IF NOT EXISTS finalproject_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE finalproject_pos;
CREATE TABLE IF NOT EXISTS products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 stock_quantity INT NOT NULL DEFAULT 0,
 image VARCHAR(255) NULL,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL,
 CONSTRAINT chk_product_price CHECK (price >= 0),
 CONSTRAINT chk_product_stock CHECK (stock_quantity >= 0)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS customers (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(100) NOT NULL,
 email VARCHAR(100) NOT NULL,
 phone VARCHAR(20) NULL,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) NOT NULL UNIQUE,
 full_name VARCHAR(100) NOT NULL,
 password VARCHAR(255) NOT NULL,
 avatar VARCHAR(255) NULL,
 role VARCHAR(10) NOT NULL,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL,
 CONSTRAINT chk_user_role CHECK (role IN ('admin','cashier'))
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS sales (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 product_id INT UNSIGNED NOT NULL,
 customer_id INT UNSIGNED NULL,
 sold_by INT UNSIGNED NOT NULL,
 quantity INT NOT NULL,
 total_price DECIMAL(10,2) NOT NULL,
 created_at DATETIME NOT NULL,
 INDEX idx_sales_product (product_id),
 INDEX idx_sales_customer (customer_id),
 INDEX idx_sales_staff (sold_by),
 CONSTRAINT fk_sales_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
 CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
 CONSTRAINT fk_sales_staff FOREIGN KEY (sold_by) REFERENCES users(id) ON DELETE RESTRICT,
 CONSTRAINT chk_sales_qty CHECK (quantity > 0),
 CONSTRAINT chk_sales_total CHECK (total_price >= 0)
) ENGINE=InnoDB;
INSERT INTO users (username,full_name,password,role,created_at)
SELECT 'demo_admin','Demo Administrator','$2y$12$Cfq90LsNjQns7fzED4HY5e3R1CyB70HK7kuwM3VmC9HuD/Zp/.h2C','admin',NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username='demo_admin');
INSERT INTO users (username,full_name,password,role,created_at)
SELECT 'demo_cashier','Demo Cashier','$2y$12$y8Wkz5aYYp63aA6DTDFXg.kJcMk7YFJvV8Lq4In5Po8iovu.vDzZC','cashier',NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username='demo_cashier');
INSERT INTO products (name,price,stock_quantity,created_at)
SELECT 'Classic Cheeseburger',149.00,24,NOW() WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Classic Cheeseburger');
INSERT INTO products (name,price,stock_quantity,created_at)
SELECT 'Chicken Sandwich',129.00,18,NOW() WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Chicken Sandwich');
INSERT INTO products (name,price,stock_quantity,created_at)
SELECT 'Iced Latte',110.00,32,NOW() WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Iced Latte');
INSERT INTO products (name,price,stock_quantity,created_at)
SELECT 'Chocolate Milkshake',135.00,15,NOW() WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Chocolate Milkshake');
INSERT INTO products (name,price,stock_quantity,created_at)
SELECT 'Blueberry Cheesecake',165.00,9,NOW() WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Blueberry Cheesecake');
INSERT INTO products (name,price,stock_quantity,created_at)
SELECT 'French Fries',85.00,27,NOW() WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='French Fries');
INSERT INTO products (name,price,stock_quantity,created_at)
SELECT 'Iced Tea',65.00,40,NOW() WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Iced Tea');
INSERT INTO products (name,price,stock_quantity,created_at)
SELECT 'Apple Pie',95.00,12,NOW() WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Apple Pie');
UPDATE products SET image='assets/classic-cheeseburger.svg' WHERE name='Classic Cheeseburger' AND image IS NULL;
UPDATE products SET image='assets/chicken-sandwich.svg' WHERE name='Chicken Sandwich' AND image IS NULL;
UPDATE products SET image='assets/iced-latte.svg' WHERE name='Iced Latte' AND image IS NULL;
UPDATE products SET image='assets/chocolate-milkshake.svg' WHERE name='Chocolate Milkshake' AND image IS NULL;
UPDATE products SET image='assets/blueberry-cheesecake.svg' WHERE name='Blueberry Cheesecake' AND image IS NULL;
UPDATE products SET image='assets/french-fries.svg' WHERE name='French Fries' AND image IS NULL;
UPDATE products SET image='assets/iced-tea.svg' WHERE name='Iced Tea' AND image IS NULL;
UPDATE products SET image='assets/apple-pie.svg' WHERE name='Apple Pie' AND image IS NULL;
