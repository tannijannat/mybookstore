-- Create database
CREATE DATABASE IF NOT EXISTS bookbuddy CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE bookbuddy;

-- Users table
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('guest', 'user', 'author', 'admin') DEFAULT 'user',
  `is_verified` TINYINT(1) DEFAULT 0,
  `verification_code` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories table
CREATE TABLE `categories` (
  `category_id` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Books table
CREATE TABLE `books` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL,
  `category_id` INT DEFAULT NULL,
  `uploaded_by` INT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart table
CREATE TABLE `cart` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `book_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE `orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `address` TEXT NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
 ALTER TABLE orders 
ADD COLUMN mobile VARCHAR(20) DEFAULT NULL;

-- Order Items table
CREATE TABLE `order_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `book_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Categories
INSERT INTO categories (category_name) VALUES 


('Classic Novels'),
('Fantasy & Adventure'),
('Motivational & Self-Help'),
('Historical & Dystopian Fiction');


-- Sample Users
INSERT INTO users (name, email, password, role, is_verified) VALUES
('Alice Johnson', 'alice@example.com', 'password123', 'user', 1),
('Bob Writer', 'bob@example.com', 'password123', 'author', 1),
('Admin User', 'admin@example.com', 'adminpass', 'admin', 1);

-- Sample Books
INSERT INTO books (title, author, price, stock, category_id, uploaded_by, image) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 1099, 5, 1, 2, 'images/grayson.jpg'),
('The beauty', 'George Orwell', 850, 10, 1, 2, 'images/autumn.jpg'),
('Nature', 'Robert C. Martin', 250.00, 3, 4, 2, 'images/Lore.jpg'),
('A Brief History of Time', 'Stephen Hawking', 1599, 7, 3, 2, 'images/time melt.jpg');

--discription text
ALTER TABLE books ADD description TEXT;
--order details
CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL
);
--notun reveiw tavle create

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK(rating BETWEEN 1 AND 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
--eta review te
ALTER TABLE reviews
ADD CONSTRAINT fk_book_id
FOREIGN KEY (book_id) REFERENCES books(id);

--eta orders table e 

ALTER TABLE orders ADD COLUMN book_id INT NOT NULL;
ALTER TABLE books
ADD COLUMN category VARCHAR(255) AFTER author;