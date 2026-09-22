CREATE DATABASE IF NOT EXISTS millz_games
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE millz_games;

-- ADMINS
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    type ENUM('game','app') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- GAMES
CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT,
    platform VARCHAR(100),
    price DECIMAL(10,2) DEFAULT NULL,
    cover_image VARCHAR(255),
    game_file VARCHAR(255),
    status ENUM('published','unpublished') DEFAULT 'unpublished',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- APPS
CREATE TABLE apps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT,
    platform VARCHAR(100),
    price DECIMAL(10,2) DEFAULT NULL,
    cover_image VARCHAR(255),
    app_file VARCHAR(255),
    status ENUM('published','unpublished') DEFAULT 'unpublished',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- CUSTOMERS
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    phone VARCHAR(30),
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PAYMENTS
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(50),
    transaction_reference VARCHAR(150),
    status ENUM('pending','confirmed','failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- ACCESS CODES
CREATE TABLE access_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) NOT NULL UNIQUE,
    customer_id INT,
    payment_id INT,
    expires_at DATETIME NOT NULL,
    status ENUM('active','used','expired','disabled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL
);

-- SALES
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    game_id INT NULL,
    app_id INT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE SET NULL,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE SET NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL
);

-- GAME REQUESTS
CREATE TABLE game_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_name VARCHAR(200) NOT NULL,
    platform VARCHAR(100),
    message TEXT,
    customer_name VARCHAR(150),
    customer_phone VARCHAR(30),
    status ENUM('pending','reviewed','completed','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- MESSAGES
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    phone VARCHAR(30),
    email VARCHAR(150),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('unread','read','replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SETTINGS
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- GAME CATEGORIES
INSERT INTO categories (name, type) VALUES
('Action','game'),
('Adventure','game'),
('Puzzle','game'),
('Racing','game'),
('Sports','game'),
('Strategy','game'),
('Simulation','game'),
('Arcade','game'),
('Card','game'),
('Casual','game'),
('Family','game'),
('Music','game'),
('RPG / Role Playing','game'),
('Fighting','game'),
('Shooter','game'),
('Multiplayer','game'),
('Popular','game'),
('New & Updated','game');

-- APP CATEGORIES
INSERT INTO categories (name, type) VALUES
('Social','app'),
('Communication','app'),
('Entertainment','app'),
('Music & Audio','app'),
('Video Players & Editors','app'),
('Photography','app'),
('Education','app'),
('Productivity','app'),
('Business','app'),
('Finance','app'),
('Tools','app'),
('Security','app'),
('File Management','app'),
('Lifestyle','app');