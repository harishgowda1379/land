-- Database: land_chain
-- Use this SQL script to create the required tables for the land registration system.

CREATE DATABASE IF NOT EXISTS land_chain;
USE land_chain;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS lands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_name VARCHAR(120) NOT NULL,
    location VARCHAR(255) NOT NULL,
    survey_number VARCHAR(100) NOT NULL UNIQUE,
    area VARCHAR(80) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    land_id INT NOT NULL,
    seller VARCHAR(120) NOT NULL,
    buyer VARCHAR(120) NOT NULL,
    date DATETIME NOT NULL,
    current_hash VARCHAR(255) NOT NULL,
    previous_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (land_id) REFERENCES lands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample land record and first transaction block
INSERT INTO lands (owner_name, location, survey_number, area, created_at)
VALUES ('Alice Johnson', 'Block B, Sector 14, Cityview', 'SVY-1001', '2500 sq.ft', NOW());

INSERT INTO transactions (land_id, seller, buyer, date, current_hash, previous_hash, created_at)
VALUES (1, 'Alice Johnson', 'Alice Johnson', '2026-01-01 12:00:00', '4201c703342af68bd4852d111bbec29ee677fe229d40694a4a39f00977216ece', '0', NOW());

-- Note: Register a user through the application to create a valid hashed password entry.
