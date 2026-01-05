-- E-Commerce Project Database Schema
-- Database: usersystemdb
-- Created: January 4, 2026

-- Create Database
CREATE DATABASE IF NOT EXISTS `usersystemdb`;
USE `usersystemdb`;

-- ============================================
-- Table: users
-- Purpose: Store user account information
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: products
-- Purpose: Store product information
-- ============================================
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255),
  `stock` INT DEFAULT 100,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_category` (`category`),
  INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: cart
-- Purpose: Store shopping cart items
-- ============================================
CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(10, 2) NOT NULL,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: orders
-- Purpose: Store customer orders
-- ============================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
  `shipping_address` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_order_date` (`order_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: order_items
-- Purpose: Store individual items in orders
-- ============================================
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10, 2) NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data: Users
-- ============================================
INSERT INTO `users` (`username`, `email`, `password`) VALUES
('john_doe', 'john@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRj2Do'),
('jane_smith', 'jane@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRj2Do'),
('admin_user', 'admin@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRj2Do');

-- ============================================
-- Sample Data: Products
-- ============================================
INSERT INTO `products` (`name`, `category`, `price`, `description`, `stock`) VALUES
('Laptop Pro', 'Electronics', 999.99, 'High-performance laptop for professionals', 15),
('Wireless Mouse', 'Electronics', 29.99, 'Ergonomic wireless mouse with 2.4GHz connectivity', 50),
('USB-C Cable', 'Accessories', 12.99, 'Durable USB-C charging and data cable', 100),
('Phone Case', 'Accessories', 19.99, 'Protective phone case with shockproof design', 75),
('Desk Lamp', 'Home', 49.99, 'LED desk lamp with adjustable brightness', 30),
('Office Chair', 'Furniture', 249.99, 'Ergonomic office chair with lumbar support', 10),
('Keyboard', 'Electronics', 79.99, 'Mechanical keyboard with RGB backlighting', 25),
('Monitor', 'Electronics', 299.99, '27-inch 4K UHD monitor', 12),
('Headphones', 'Electronics', 149.99, 'Noise-cancelling wireless headphones', 20),
('Desk Organizer', 'Home', 34.99, 'Multi-compartment desk organizer', 40);

-- ============================================
-- Sample Data: Cart Items
-- ============================================
INSERT INTO `cart` (`user_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 999.99),
(1, 2, 2, 29.99),
(2, 5, 1, 49.99),
(2, 7, 1, 79.99);

-- ============================================
-- Sample Data: Orders
-- ============================================
INSERT INTO `orders` (`user_id`, `total_amount`, `status`, `shipping_address`) VALUES
(1, 1059.97, 'delivered', '123 Main St, New York, NY 10001'),
(2, 199.97, 'shipped', '456 Oak Ave, Los Angeles, CA 90001'),
(1, 349.98, 'processing', '123 Main St, New York, NY 10001');

-- ============================================
-- Sample Data: Order Items
-- ============================================
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 1, 999.99, 999.99),
(1, 2, 2, 29.99, 59.98),
(2, 5, 1, 49.99, 49.99),
(2, 7, 1, 79.99, 79.99),
(3, 3, 3, 12.99, 38.97),
(3, 4, 2, 19.99, 39.98),
(3, 9, 1, 149.99, 149.99);

-- ============================================
-- Create Indexes for Better Performance
-- ============================================
ALTER TABLE `products` ADD FULLTEXT INDEX `ft_name_description` (`name`, `description`);
ALTER TABLE `users` ADD INDEX `idx_created_at` (`created_at`);
ALTER TABLE `orders` ADD INDEX `idx_status` (`status`);

-- ============================================
-- Database Setup Complete
-- ============================================
-- Database Name: usersystemdb
-- Total Tables: 5 (users, products, cart, orders, order_items)
-- Sample Users: 3
-- Sample Products: 10
-- Sample Orders: 3
-- ============================================
