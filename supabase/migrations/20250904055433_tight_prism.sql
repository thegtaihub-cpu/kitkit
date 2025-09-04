-- KITKIT Shopping Database Setup
-- Compatible with Hostinger and most MySQL servers

CREATE DATABASE IF NOT EXISTS kitkit_shopping CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kitkit_shopping;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    address TEXT,
    pincode VARCHAR(10),
    city VARCHAR(50),
    state VARCHAR(50),
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    password_hash VARCHAR(255) NOT NULL,
    reset_token VARCHAR(100) NULL,
    reset_token_expires DATETIME NULL,
    email_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) NULL,
    category VARCHAR(50) NOT NULL,
    image_url VARCHAR(500),
    weight VARCHAR(50),
    stock_quantity INT DEFAULT 100,
    rating DECIMAL(3,2) DEFAULT 4.5,
    review_count INT DEFAULT 0,
    is_organic TINYINT(1) DEFAULT 0,
    discount_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_id VARCHAR(100),
    delivery_address TEXT,
    delivery_pincode VARCHAR(10),
    delivery_city VARCHAR(50),
    delivery_state VARCHAR(50),
    delivery_date DATE,
    delivery_time VARCHAR(50),
    tracking_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    is_verified_purchase TINYINT(1) DEFAULT 0,
    admin_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wishlist table
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    emergency_access TINYINT(1) DEFAULT 0,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Website settings table
CREATE TABLE website_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Banner images table
CREATE TABLE banner_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    image_url VARCHAR(500) NOT NULL,
    link_url VARCHAR(500),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indian cities and pincodes table
CREATE TABLE indian_cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL,
    state_name VARCHAR(100) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    INDEX idx_pincode (pincode),
    INDEX idx_city (city_name),
    INDEX idx_state (state_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample categories
INSERT INTO categories (name, description, image_url, sort_order) VALUES
('Fresh Fruits', 'Fresh seasonal fruits from local farms', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', 1),
('Vegetables', 'Fresh vegetables and leafy greens', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', 2),
('Dairy Products', 'Fresh milk, yogurt, cheese and dairy items', 'https://images.pexels.com/photos/236010/pexels-photo-236010.jpeg', 3),
('Grains & Cereals', 'Rice, wheat, pulses and cereals', 'https://images.pexels.com/photos/4198019/pexels-photo-4198019.jpeg', 4),
('Spices & Condiments', 'Indian spices and cooking essentials', 'https://images.pexels.com/photos/4198019/pexels-photo-4198019.jpeg', 5),
('Snacks & Beverages', 'Healthy snacks and refreshing drinks', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', 6);

-- Insert sample products (30 products across categories)
INSERT INTO products (name, description, price, original_price, category, image_url, weight, stock_quantity, rating, review_count, is_organic, discount_percentage) VALUES
-- Fresh Fruits (10 products)
('Fresh Bananas', 'Sweet and ripe bananas from Kerala farms', 45.00, 50.00, 'Fresh Fruits', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', '1 kg', 100, 4.5, 45, 1, 10),
('Red Apples', 'Crispy red apples from Himachal Pradesh', 120.00, 140.00, 'Fresh Fruits', 'https://images.pexels.com/photos/102104/pexels-photo-102104.jpeg', '1 kg', 80, 4.7, 67, 0, 14),
('Fresh Oranges', 'Juicy oranges packed with Vitamin C', 80.00, 90.00, 'Fresh Fruits', 'https://images.pexels.com/photos/161559/background-bitter-breakfast-bright-161559.jpeg', '1 kg', 90, 4.6, 34, 1, 11),
('Alphonso Mangoes', 'Premium Alphonso mangoes from Maharashtra', 250.00, 300.00, 'Fresh Fruits', 'https://images.pexels.com/photos/918327/pexels-photo-918327.jpeg', '1 kg', 50, 4.9, 89, 1, 17),
('Fresh Grapes', 'Sweet green grapes perfect for snacking', 150.00, 170.00, 'Fresh Fruits', 'https://images.pexels.com/photos/708777/pexels-photo-708777.jpeg', '500g', 60, 4.4, 23, 0, 12),
('Pomegranates', 'Fresh pomegranates rich in antioxidants', 180.00, 200.00, 'Fresh Fruits', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', '1 kg', 40, 4.8, 56, 1, 10),
('Fresh Pineapple', 'Sweet and tangy pineapple', 60.00, 70.00, 'Fresh Fruits', 'https://images.pexels.com/photos/947879/pexels-photo-947879.jpeg', '1 piece', 30, 4.3, 12, 0, 14),
('Watermelon', 'Refreshing watermelon perfect for summer', 40.00, 45.00, 'Fresh Fruits', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', '1 kg', 70, 4.2, 28, 0, 11),
('Fresh Papaya', 'Ripe papaya rich in vitamins', 50.00, 60.00, 'Fresh Fruits', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', '1 kg', 55, 4.5, 19, 1, 17),
('Kiwi Fruits', 'Exotic kiwi fruits imported fresh', 200.00, 220.00, 'Fresh Fruits', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', '500g', 25, 4.6, 15, 0, 9),

-- Vegetables (10 products)
('Fresh Tomatoes', 'Red ripe tomatoes perfect for cooking', 30.00, 35.00, 'Vegetables', 'https://images.pexels.com/photos/533280/pexels-photo-533280.jpeg', '1 kg', 120, 4.4, 78, 1, 14),
('Green Onions', 'Fresh green onions for garnishing', 25.00, 30.00, 'Vegetables', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', '250g', 100, 4.2, 34, 0, 17),
('Fresh Potatoes', 'Quality potatoes from Punjab farms', 35.00, 40.00, 'Vegetables', 'https://images.pexels.com/photos/144248/potatoes-vegetables-erdfrucht-bio-144248.jpeg', '1 kg', 150, 4.3, 56, 1, 13),
('Spinach Leaves', 'Fresh green spinach leaves', 20.00, 25.00, 'Vegetables', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', '250g', 80, 4.6, 23, 1, 20),
('Carrots', 'Fresh orange carrots rich in beta-carotene', 40.00, 45.00, 'Vegetables', 'https://images.pexels.com/photos/143133/pexels-photo-143133.jpeg', '1 kg', 90, 4.5, 41, 0, 11),
('Bell Peppers', 'Colorful bell peppers for salads', 80.00, 90.00, 'Vegetables', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', '500g', 60, 4.7, 29, 0, 11),
('Fresh Cauliflower', 'White cauliflower perfect for curries', 45.00, 50.00, 'Vegetables', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', '1 piece', 70, 4.4, 18, 1, 10),
('Green Beans', 'Fresh green beans for healthy cooking', 60.00, 70.00, 'Vegetables', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', '500g', 85, 4.3, 22, 0, 14),
('Fresh Broccoli', 'Nutritious broccoli florets', 90.00, 100.00, 'Vegetables', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', '500g', 45, 4.8, 31, 1, 10),
('Red Onions', 'Fresh red onions for cooking', 35.00, 40.00, 'Vegetables', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', '1 kg', 110, 4.2, 67, 0, 13),

-- Dairy Products (5 products)
('Fresh Milk', 'Pure cow milk from local dairy', 55.00, 60.00, 'Dairy Products', 'https://images.pexels.com/photos/236010/pexels-photo-236010.jpeg', '1 liter', 200, 4.6, 123, 1, 8),
('Paneer', 'Fresh cottage cheese made daily', 180.00, 200.00, 'Dairy Products', 'https://images.pexels.com/photos/236010/pexels-photo-236010.jpeg', '250g', 50, 4.8, 89, 1, 10),
('Greek Yogurt', 'Thick and creamy Greek yogurt', 120.00, 130.00, 'Dairy Products', 'https://images.pexels.com/photos/236010/pexels-photo-236010.jpeg', '400g', 75, 4.7, 45, 1, 8),
('Fresh Butter', 'Creamy butter made from pure cream', 250.00, 280.00, 'Dairy Products', 'https://images.pexels.com/photos/236010/pexels-photo-236010.jpeg', '500g', 40, 4.5, 67, 0, 11),
('Cheese Slices', 'Processed cheese slices for sandwiches', 150.00, 170.00, 'Dairy Products', 'https://images.pexels.com/photos/236010/pexels-photo-236010.jpeg', '200g', 60, 4.4, 34, 0, 12),

-- Grains & Cereals (5 products)
('Basmati Rice', 'Premium aged basmati rice', 180.00, 200.00, 'Grains & Cereals', 'https://images.pexels.com/photos/4198019/pexels-photo-4198019.jpeg', '1 kg', 100, 4.7, 156, 1, 10),
('Whole Wheat Flour', 'Fresh ground wheat flour', 45.00, 50.00, 'Grains & Cereals', 'https://images.pexels.com/photos/4198019/pexels-photo-4198019.jpeg', '1 kg', 150, 4.5, 89, 1, 10),
('Toor Dal', 'Premium quality toor dal', 120.00, 130.00, 'Grains & Cereals', 'https://images.pexels.com/photos/4198019/pexels-photo-4198019.jpeg', '1 kg', 80, 4.6, 67, 0, 8),
('Oats', 'Healthy rolled oats for breakfast', 180.00, 200.00, 'Grains & Cereals', 'https://images.pexels.com/photos/4198019/pexels-photo-4198019.jpeg', '1 kg', 60, 4.8, 45, 1, 10),
('Quinoa', 'Organic quinoa superfood', 450.00, 500.00, 'Grains & Cereals', 'https://images.pexels.com/photos/4198019/pexels-photo-4198019.jpeg', '500g', 30, 4.9, 23, 1, 10),

-- Sample Indian cities with pincodes
INSERT INTO indian_cities (city_name, state_name, pincode, latitude, longitude) VALUES
('Mumbai', 'Maharashtra', '400001', 19.0760, 72.8777),
('Delhi', 'Delhi', '110001', 28.7041, 77.1025),
('Bangalore', 'Karnataka', '560001', 12.9716, 77.5946),
('Chennai', 'Tamil Nadu', '600001', 13.0827, 80.2707),
('Kolkata', 'West Bengal', '700001', 22.5726, 88.3639),
('Hyderabad', 'Telangana', '500001', 17.3850, 78.4867),
('Pune', 'Maharashtra', '411001', 18.5204, 73.8567),
('Ahmedabad', 'Gujarat', '380001', 23.0225, 72.5714),
('Jaipur', 'Rajasthan', '302001', 26.9124, 75.7873),
('Lucknow', 'Uttar Pradesh', '226001', 26.8467, 80.9462),
('Kanpur', 'Uttar Pradesh', '208001', 26.4499, 80.3319),
('Nagpur', 'Maharashtra', '440001', 21.1458, 79.0882),
('Indore', 'Madhya Pradesh', '452001', 22.7196, 75.8577),
('Thane', 'Maharashtra', '400601', 19.2183, 72.9781),
('Bhopal', 'Madhya Pradesh', '462001', 23.2599, 77.4126),
('Visakhapatnam', 'Andhra Pradesh', '530001', 17.6868, 83.2185),
('Pimpri-Chinchwad', 'Maharashtra', '411017', 18.6298, 73.7997),
('Patna', 'Bihar', '800001', 25.5941, 85.1376),
('Vadodara', 'Gujarat', '390001', 22.3072, 73.1812),
('Ghaziabad', 'Uttar Pradesh', '201001', 28.6692, 77.4538);

-- Insert sample admin user
INSERT INTO admin_users (username, email, password_hash, emergency_access) VALUES
('admin', 'admin@kitkitshopping.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('emergency', 'emergency@kitkitshopping.com', '', 1);

-- Insert sample users
INSERT INTO users (name, email, mobile, address, pincode, city, state, password_hash) VALUES
('Rajesh Kumar', 'rajesh@example.com', '9876543210', '123 MG Road, Sector 15', '400001', 'Mumbai', 'Maharashtra', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Priya Sharma', 'priya@example.com', '9876543211', '456 Park Street, Block A', '110001', 'Delhi', 'Delhi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert website settings
INSERT INTO website_settings (setting_key, setting_value) VALUES
('site_name', 'KITKIT Shopping'),
('whatsapp_number', '919876543210'),
('upi_id', 'kitkitshopping@paytm'),
('razorpay_key_id', 'rzp_test_1234567890'),
('razorpay_key_secret', 'your_razorpay_secret_key'),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_username', 'your_email@gmail.com'),
('smtp_password', 'your_app_password'),
('theme_primary_color', '#2D5A27'),
('theme_secondary_color', '#F4A261');

-- Insert sample banner images
INSERT INTO banner_images (title, image_url, link_url, sort_order) VALUES
('Summer Sale', 'https://images.pexels.com/photos/1435904/pexels-photo-1435904.jpeg', 'product_categories.php', 1),
('Fresh Fruits Offer', 'https://images.pexels.com/photos/1300972/pexels-photo-1300972.jpeg', 'product_categories.php?category=Fresh%20Fruits', 2),
('Organic Products', 'https://images.pexels.com/photos/4198019/pexels-photo-4198019.jpeg', 'product_categories.php', 3);

-- Create indexes for better performance
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
CREATE INDEX idx_users_email ON users(email);