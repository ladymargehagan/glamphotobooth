-- Photography Marketplace Database Schema
-- All tables prefixed with pb_

-- Drop tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS pb_gallery_photos;
DROP TABLE IF EXISTS pb_photo_galleries;
DROP TABLE IF EXISTS pb_reviews;
DROP TABLE IF EXISTS pb_order_items;
DROP TABLE IF EXISTS pb_orders;
DROP TABLE IF EXISTS pb_cart;
DROP TABLE IF EXISTS pb_bookings;
DROP TABLE IF EXISTS pb_products;
DROP TABLE IF EXISTS pb_service_providers;
DROP TABLE IF EXISTS pb_categories;
DROP TABLE IF EXISTS pb_admin;
DROP TABLE IF EXISTS pb_customer;

-- Table: pb_customer
-- Stores all users (customers, photographers, vendors)
CREATE TABLE pb_customer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    country VARCHAR(100),
    city VARCHAR(100),
    contact VARCHAR(50),
    image VARCHAR(500),
    user_role TINYINT NOT NULL DEFAULT 4 COMMENT '2=photographer, 3=vendor, 4=customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_user_role (user_role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_admin
-- Stores admin users separately
CREATE TABLE pb_admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin' COMMENT 'admin role type',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_categories
-- Product/service categories
CREATE TABLE pb_categories (
    cat_id INT AUTO_INCREMENT PRIMARY KEY,
    cat_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cat_name (cat_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_service_providers
-- Photographer and vendor business profiles
CREATE TABLE pb_service_providers (
    provider_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    description TEXT,
    hourly_rate DECIMAL(10, 2),
    portfolio_images TEXT COMMENT 'JSON array of image paths',
    rating DECIMAL(3, 2) DEFAULT 0.00 COMMENT 'Average rating 0.00-5.00',
    total_reviews INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES pb_customer(id) ON DELETE CASCADE,
    INDEX idx_customer_id (customer_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_products
-- Products and services offered
CREATE TABLE pb_products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    cat_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    product_type ENUM('service', 'rental', 'sale') NOT NULL,
    image VARCHAR(500),
    keywords VARCHAR(500) COMMENT 'Comma-separated keywords for search',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES pb_service_providers(provider_id) ON DELETE CASCADE,
    FOREIGN KEY (cat_id) REFERENCES pb_categories(cat_id) ON DELETE RESTRICT,
    INDEX idx_provider_id (provider_id),
    INDEX idx_cat_id (cat_id),
    INDEX idx_product_type (product_type),
    INDEX idx_is_active (is_active),
    FULLTEXT INDEX idx_search (title, description, keywords)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_bookings
-- Service bookings/appointments
CREATE TABLE pb_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    duration_hours DECIMAL(4, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES pb_customer(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES pb_products(product_id) ON DELETE RESTRICT,
    INDEX idx_customer_id (customer_id),
    INDEX idx_product_id (product_id),
    INDEX idx_status (status),
    INDEX idx_booking_date (booking_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_cart
-- Shopping cart items
CREATE TABLE pb_cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES pb_customer(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES pb_products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (customer_id, product_id),
    INDEX idx_customer_id (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_orders
-- Customer orders
CREATE TABLE pb_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_reference VARCHAR(255),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES pb_customer(id) ON DELETE RESTRICT,
    INDEX idx_customer_id (customer_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_order_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_order_items
-- Individual items in an order
CREATE TABLE pb_order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL COMMENT 'Price at time of purchase',
    FOREIGN KEY (order_id) REFERENCES pb_orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES pb_products(product_id) ON DELETE RESTRICT,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_photo_galleries
-- Photo galleries for bookings
CREATE TABLE pb_photo_galleries (
    gallery_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    access_code VARCHAR(50) NOT NULL UNIQUE,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES pb_bookings(booking_id) ON DELETE CASCADE,
    INDEX idx_booking_id (booking_id),
    INDEX idx_access_code (access_code),
    INDEX idx_expiry_date (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_gallery_photos
-- Photos in a gallery
CREATE TABLE pb_gallery_photos (
    photo_id INT AUTO_INCREMENT PRIMARY KEY,
    gallery_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gallery_id) REFERENCES pb_photo_galleries(gallery_id) ON DELETE CASCADE,
    INDEX idx_gallery_id (gallery_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pb_reviews
-- Customer reviews for service providers
CREATE TABLE pb_reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    provider_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES pb_customer(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES pb_service_providers(provider_id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (customer_id, provider_id),
    INDEX idx_provider_id (provider_id),
    INDEX idx_rating (rating),
    INDEX idx_review_date (review_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO pb_categories (cat_name) VALUES
('Wedding'),
('Portrait'),
('Event'),
('Photobooth Rental'),
('Photobooth Sales'),
('Prints');

