-- QuickCart Pro Database Setup
-- Run this SQL script to create the database and tables

CREATE DATABASE IF NOT EXISTS quickcart_pro;
USE quickcart_pro;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    category VARCHAR(50) NOT NULL,
    image_url VARCHAR(255),
    weight VARCHAR(50),
    stock_quantity INT DEFAULT 100,
    rating DECIMAL(3,2) DEFAULT 4.5,
    review_count INT DEFAULT 0,
    is_organic BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    delivery_address TEXT,
    delivery_date DATE,
    delivery_time VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample categories
INSERT INTO categories (name, description, image_url) VALUES
('Fresh Produce', 'Fresh fruits and vegetables', 'https://images.unsplash.com/photo-1542838132-92c53300491e'),
('Dairy & Eggs', 'Fresh dairy products and farm eggs', 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2'),
('Bakery', 'Fresh breads and baked goods', 'https://images.unsplash.com/photo-1509440159596-0249088772ff'),
('Meat & Seafood', 'Fresh meats and seafood', 'https://images.unsplash.com/photo-1546833999-b9f581a1996d'),
('Pantry Staples', 'Essential pantry items', 'https://images.unsplash.com/photo-1586201375761-83865001e31c');

-- Insert sample products
INSERT INTO products (name, description, price, original_price, category, image_url, weight, stock_quantity, rating, review_count, is_organic) VALUES
('Organic Bananas', 'Fresh, ripe organic bananas from certified farms', 2.99, 3.99, 'Fresh Produce', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', '1 lb', 50, 4.8, 324, TRUE),
('Whole Grain Bread', 'Artisan whole grain bread baked fresh daily', 4.49, NULL, 'Bakery', 'https://images.pixabay.com/photo/2016/08/11/08/04/bread-1585602_1280.jpg', '24 oz loaf', 25, 4.6, 189, FALSE),
('Fresh Milk', 'Organic whole milk from local dairy farms', 3.79, NULL, 'Dairy & Eggs', 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2', '1 gallon', 75, 4.7, 156, TRUE),
('Mixed Berries', 'Fresh strawberries, blueberries, and raspberries', 6.99, NULL, 'Fresh Produce', 'https://images.pixabay.com/photo/2017/05/11/19/44/fresh-fruits-2305192_1280.jpg', '12 oz pack', 30, 4.9, 234, TRUE),
('Greek Yogurt', 'Creamy Greek yogurt with live probiotics', 5.49, NULL, 'Dairy & Eggs', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', '32 oz container', 40, 4.5, 178, TRUE),
('Farm Fresh Eggs', 'Free-range eggs from local farms', 4.99, NULL, 'Dairy & Eggs', 'https://images.pixabay.com/photo/2016/12/06/18/27/eggs-1887522_1280.jpg', '12 count', 60, 4.8, 298, TRUE),
('Premium Avocados', 'Fresh, creamy Hass avocados ready to eat', 2.99, 3.99, 'Fresh Produce', 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43', '1 piece', 35, 4.8, 324, TRUE),
('Artisan Sourdough', 'Traditional sourdough bread with tangy flavor', 5.99, NULL, 'Bakery', 'https://images.pixabay.com/photo/2016/08/11/08/04/bread-1585602_1280.jpg', '1 loaf', 20, 4.7, 145, FALSE),
('Himalayan Sea Salt', 'Pure Himalayan pink salt for cooking', 3.49, NULL, 'Pantry Staples', 'https://images.unsplash.com/photo-1571212515416-fef01fc43637', '1 lb', 100, 4.6, 87, FALSE),
('Fresh Limes', 'Juicy limes perfect for cooking and drinks', 2.49, NULL, 'Fresh Produce', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', '1 lb bag', 45, 4.5, 123, FALSE),
('Cherry Tomatoes', 'Sweet cherry tomatoes on the vine', 3.99, NULL, 'Fresh Produce', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4', '1 pint', 55, 4.7, 167, TRUE),
('Red Onion', 'Fresh red onions for cooking', 1.29, NULL, 'Fresh Produce', 'https://images.pixabay.com/photo/2017/05/11/19/44/fresh-fruits-2305192_1280.jpg', '1 medium', 80, 4.4, 89, FALSE);

-- Insert sample users
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, address, city, state, zip_code) VALUES
('john_smith', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', '(555) 123-4567', '1234 Pine Street, Apt 5B', 'Seattle', 'WA', '98101'),
('sarah_johnson', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Johnson', '(555) 987-6543', '5678 Oak Avenue', 'Seattle', 'WA', '98102'),
('mike_rodriguez', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Rodriguez', '(555) 456-7890', '9012 Elm Drive', 'Seattle', 'WA', '98103');

-- Insert sample reviews
INSERT INTO reviews (product_id, user_id, rating, comment, is_verified_purchase) VALUES
(1, 1, 5, 'Perfect ripeness and amazing quality! These avocados were exactly what I expected - creamy, flavorful, and fresh.', TRUE),
(1, 2, 5, 'Excellent organic bananas! The subscription service is convenient and the quality is consistently high.', TRUE),
(2, 1, 4, 'Great bread, fresh and tasty. Perfect for morning toast.', TRUE),
(3, 3, 5, 'Fresh milk, great taste and quality. Delivered quickly.', TRUE),
(4, 2, 5, 'Love these mixed berries! Perfect for smoothies and breakfast.', TRUE);

-- Insert sample orders
INSERT INTO orders (user_id, order_number, total_amount, status, payment_status, payment_method, delivery_address, delivery_date) VALUES
(1, 'QCP-2025-001', 17.93, 'delivered', 'paid', 'visa_4242', '1234 Pine Street, Apt 5B, Seattle, WA 98101', '2025-01-02'),
(2, 'QCP-2025-002', 23.47, 'processing', 'paid', 'apple_pay', '5678 Oak Avenue, Seattle, WA 98102', '2025-01-03'),
(3, 'QCP-2025-003', 14.26, 'pending', 'pending', 'klarna', '9012 Elm Drive, Seattle, WA 98103', '2025-01-04');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES
(1, 1, 2, 2.99, 5.98),
(1, 2, 1, 4.49, 4.49),
(1, 3, 1, 3.79, 3.79),
(2, 4, 1, 6.99, 6.99),
(2, 5, 1, 5.49, 5.49),
(2, 6, 1, 4.99, 4.99),
(3, 1, 1, 2.99, 2.99),
(3, 7, 2, 2.99, 5.98);

-- Create indexes for better performance
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);