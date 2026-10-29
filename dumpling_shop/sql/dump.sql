CREATE DATABASE IF NOT EXISTS dumpling_shop
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dumpling_shop;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  address TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(60) NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  weight INT,
  is_available TINYINT(1) DEFAULT 1
);

CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  total DECIMAL(10,2) NOT NULL,
  status ENUM('new','cooking','delivered','cancelled') DEFAULT 'new',
  address TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO products (category, name, description, price, weight) VALUES
  ('Классические', 'Пельмени классические', 'Говядина + свинина, тонкое тесто', 320.00, 500),
  ('Классические', 'Пельмени домашние', 'Ручная лепка, сочные', 380.00, 500),
  ('Сибирские', 'Сибирские крупные', 'Много мяса, толстое тесто', 420.00, 600),
  ('Вареники', 'Вареники с вишней', 'Сладкая вишня, летний вкус', 290.00, 500),
  ('Вареники', 'Вареники с картошкой', 'Картофель + лук', 250.00, 500),
  ('Полуфабрикаты', 'Пельмени зам. 1кг', 'Замороженные, для дома', 560.00, 1000);