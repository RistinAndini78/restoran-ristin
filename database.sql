-- Database: restoran_ecommerce
CREATE DATABASE IF NOT EXISTS restoran_ecommerce;
USE restoran_ecommerce;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    address TEXT,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table: orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('Menunggu', 'Diproses', 'Dikirim', 'Selesai') DEFAULT 'Menunggu',
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table: order_items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert Default Admin (Password: admin123)
INSERT INTO users (username, password, email, full_name, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@restoran.com', 'Administrator', 'admin');

-- Insert Sample Categories
INSERT INTO categories (name) VALUES ('Makanan Utama'), ('Minuman'), ('Camilan'), ('Pencuci Mulut');

-- Insert Sample Products
INSERT INTO products (category_id, name, description, price, stock, image) VALUES 
(1, 'Nasi Goreng Spesial', 'Nasi goreng dengan bumbu rempah pilihan, disajikan dengan telur mata sapi, kerupuk, dan acar segar.', 35000, 50, 'nasi_goreng.png'),
(1, 'Sate Ayam Madura', 'Sate ayam empuk dengan bumbu kacang khas Madura yang kental dan gurih.', 45000, 30, 'sate_ayam.png'),
(1, 'Rendang Daging Sapi', 'Daging sapi pilihan yang dimasak lama dengan santan dan rempah hingga meresap sempurna.', 65000, 20, 'rendang.png'),
(1, 'Soto Ayam Tradisional', 'Soto ayam dengan kuah kuning bening yang segar, suwiran ayam, dan soun.', 28000, 40, 'soto_ayam.png'),
(2, 'Es Teh Manis', 'Teh seduh berkualitas yang disajikan dingin dengan gula tebu murni.', 8000, 100, 'es_teh.png'),
(2, 'Es Jeruk Peras', 'Jeruk peras segar pilihan yang kaya akan vitamin C.', 15000, 60, 'es_jeruk.png');
