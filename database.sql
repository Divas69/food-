-- Food Delivery System Database Schema
-- Run this script in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS food_delivery;
USE food_delivery;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Restaurants table
CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    address TEXT,
    phone VARCHAR(20),
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menu items table
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    status ENUM('available', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    restaurant_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
    delivery_address TEXT,
    phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO admins (username, email, password) VALUES 
('admin', 'admin@fooddelivery.com', '$2y$12$CTAuE6X/DnjkmbKUF7.aJOVy44u12X95Z3LMgReUCUGZW9T/mZYVe');

-- Insert sample restaurants
INSERT INTO restaurants (name, description, address, phone, image) VALUES 
('Pizza Palace', 'Best pizza in town with fresh ingredients', '123 Main St, City', '555-0101', 'pizza_palace.jpg'),
('Burger House', 'Gourmet burgers and fries', '456 Oak Ave, City', '555-0102', 'burger_house.jpg'),
('Sushi Zen', 'Authentic Japanese sushi and sashimi', '789 Pine St, City', '555-0103', 'sushi_zen.jpg'),
('Taco Fiesta', 'Mexican street food and tacos', '321 Elm St, City', '555-0104', 'taco_fiesta.jpg');

-- Insert sample menu items
INSERT INTO menu_items (restaurant_id, name, description, price, image, category) VALUES 
-- Pizza Palace items
(1, 'Margherita Pizza', 'Classic tomato, mozzarella, and basil', 12.99, 'margherita.jpg', 'Pizza'),
(1, 'Pepperoni Pizza', 'Pepperoni with mozzarella cheese', 14.99, 'pepperoni.jpg', 'Pizza'),
(1, 'BBQ Chicken Pizza', 'BBQ sauce, chicken, red onions', 16.99, 'bbq_chicken.jpg', 'Pizza'),
(1, 'Caesar Salad', 'Fresh romaine lettuce with caesar dressing', 8.99, 'caesar_salad.jpg', 'Salad'),

-- Burger House items
(2, 'Classic Burger', 'Beef patty with lettuce, tomato, onion', 9.99, 'classic_burger.jpg', 'Burger'),
(2, 'Cheeseburger', 'Beef patty with cheese, lettuce, tomato', 10.99, 'cheeseburger.jpg', 'Burger'),
(2, 'Bacon Burger', 'Beef patty with bacon and cheese', 12.99, 'bacon_burger.jpg', 'Burger'),
(2, 'French Fries', 'Crispy golden french fries', 4.99, 'french_fries.jpg', 'Sides'),

-- Sushi Zen items
(3, 'California Roll', 'Crab, avocado, cucumber', 8.99, 'california_roll.jpg', 'Sushi'),
(3, 'Salmon Roll', 'Fresh salmon with rice', 10.99, 'salmon_roll.jpg', 'Sushi'),
(3, 'Dragon Roll', 'Eel, cucumber, avocado', 13.99, 'dragon_roll.jpg', 'Sushi'),
(3, 'Miso Soup', 'Traditional Japanese soup', 3.99, 'miso_soup.jpg', 'Soup'),

-- Taco Fiesta items
(4, 'Beef Tacos', 'Seasoned ground beef with lettuce', 7.99, 'beef_tacos.jpg', 'Tacos'),
(4, 'Chicken Tacos', 'Grilled chicken with salsa', 7.99, 'chicken_tacos.jpg', 'Tacos'),
(4, 'Fish Tacos', 'Battered fish with cabbage slaw', 9.99, 'fish_tacos.jpg', 'Tacos'),
(4, 'Churros', 'Sweet fried dough with cinnamon', 5.99, 'churros.jpg', 'Dessert');
