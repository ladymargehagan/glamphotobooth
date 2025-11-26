-- Migration: Create Gallery Tables
-- This migration creates tables for photo gallery management

-- Gallery table
CREATE TABLE IF NOT EXISTS pb_galleries (
    gallery_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    provider_id INT NOT NULL,
    title VARCHAR(255),
    access_code VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES pb_bookings(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES pb_service_providers(provider_id) ON DELETE CASCADE,
    INDEX idx_booking_id (booking_id),
    INDEX idx_provider_id (provider_id),
    INDEX idx_access_code (access_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gallery photos table
CREATE TABLE IF NOT EXISTS pb_gallery_photos (
    photo_id INT AUTO_INCREMENT PRIMARY KEY,
    gallery_id INT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    original_name VARCHAR(255),
    photo_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gallery_id) REFERENCES pb_galleries(gallery_id) ON DELETE CASCADE,
    INDEX idx_gallery_id (gallery_id),
    INDEX idx_photo_order (photo_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
