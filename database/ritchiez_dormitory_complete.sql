CREATE DATABASE IF NOT EXISTS ritchiez_dormitory;
USE ritchiez_dormitory;

DROP TABLE IF EXISTS notifications, messages, reviews, favorites, payment_uploads, order_items, orders, reservations, room_images, products, rooms, users;

CREATE TABLE users (
 user_id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(100) NOT NULL,
 email VARCHAR(100) NOT NULL UNIQUE,
 phone VARCHAR(30),
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','tenant') DEFAULT 'tenant',
 status ENUM('Active','Blocked') DEFAULT 'Active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
 room_id INT AUTO_INCREMENT PRIMARY KEY,
 room_name VARCHAR(100) NOT NULL,
 floor VARCHAR(50) NOT NULL,
 room_type VARCHAR(50) NOT NULL,
 capacity INT NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 status ENUM('Available','Occupied','Maintenance') DEFAULT 'Available',
 image_url TEXT,
 description TEXT,
 amenities TEXT,
 rules TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE room_images (
 image_id INT AUTO_INCREMENT PRIMARY KEY,
 room_id INT NOT NULL,
 image_url TEXT NOT NULL,
 FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

CREATE TABLE reservations (
 reservation_id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 room_id INT NOT NULL,
 move_in_date DATE NOT NULL,
 occupants INT NOT NULL,
 notes TEXT,
 status ENUM('Pending','Approved','Rejected','Cancelled') DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
 FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

CREATE TABLE payment_uploads (
 payment_id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 amount DECIMAL(10,2) NOT NULL,
 payment_method VARCHAR(50) NOT NULL,
 reference_number VARCHAR(100),
 receipt_file VARCHAR(255),
 status ENUM('Pending','Verified','Rejected') DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE products (
 product_id INT AUTO_INCREMENT PRIMARY KEY,
 product_name VARCHAR(100) NOT NULL,
 description TEXT,
 price DECIMAL(10,2) NOT NULL,
 stock INT DEFAULT 0,
 image_url TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
 order_id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 total_amount DECIMAL(10,2) NOT NULL,
 payment_method VARCHAR(50) NOT NULL,
 status ENUM('Pending','Completed','Cancelled') DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE order_items (
 order_item_id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT NOT NULL,
 product_id INT NOT NULL,
 quantity INT NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
 FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

CREATE TABLE favorites (
 favorite_id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 room_id INT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE(user_id, room_id),
 FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
 FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

CREATE TABLE reviews (
 review_id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 room_id INT NOT NULL,
 rating INT NOT NULL,
 comment TEXT,
 status ENUM('Visible','Hidden') DEFAULT 'Visible',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
 FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

CREATE TABLE messages (
 message_id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(100) NOT NULL,
 email VARCHAR(100) NOT NULL,
 message TEXT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
 notification_id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 message TEXT NOT NULL,
 is_read TINYINT(1) DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

INSERT INTO users (full_name,email,phone,password,role) VALUES
('RITCHIEZ Admin','admin@ritchiez.com','09123456789','$2y$10$JejrHKFxO5aMrW/6URoxQOFfEugAW6KQJKf18umGWT8x9EHkN3sCK','admin');

INSERT INTO rooms (room_name,floor,room_type,capacity,price,status,image_url,description,amenities,rules) VALUES
('Room 101 - 1st Floor','1st Floor','Standard',2,3500,'Available','https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=1470&auto=format&fit=crop','Modern room ideal for 2 students.','WiFi, Electric Fan, Study Table','No smoking. Quiet hours after 10 PM.'),
('Room 202 - 2nd Floor','2nd Floor','Premium',3,4500,'Available','https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1470&auto=format&fit=crop','Air-conditioned premium dormitory room.','Aircon, Cabinet, WiFi','Visitors must register. Keep the room clean.'),
('Room 303 - 3rd Floor','3rd Floor','Deluxe',4,5500,'Available','https://images.unsplash.com/photo-1484154218962-a197022b5858?q=80&w=1470&auto=format&fit=crop','Spacious room with balcony access.','Balcony, Aircon, Hot Shower','No loud music. Pay monthly dues on time.');

INSERT INTO room_images (room_id,image_url) VALUES
(1,'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1470&auto=format&fit=crop'),
(2,'https://images.unsplash.com/photo-1560448075-bb485b067938?q=80&w=1470&auto=format&fit=crop'),
(3,'https://images.unsplash.com/photo-1494526585095-c41746248156?q=80&w=1470&auto=format&fit=crop');

INSERT INTO products (product_name,description,price,stock,image_url) VALUES
('Pillow & Blanket Set','Comfortable bedding package.',899,20,'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1470&auto=format&fit=crop'),
('Study Table Lamp','LED lamp for late night studying.',499,15,'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1470&auto=format&fit=crop'),
('Storage Organizer','Space-saving organizer for students.',699,10,'https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1470&auto=format&fit=crop');
