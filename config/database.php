<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lida_leads');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    $conn->select_db(DB_NAME);
    
    // Create leads table
    $sql = "CREATE TABLE IF NOT EXISTS leads (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255) NOT NULL,
        niche VARCHAR(100) NOT NULL,
        location VARCHAR(100) NOT NULL,
        contact_person VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(50),
        status ENUM('new', 'contacted', 'follow_up', 'converted', 'not_interested') DEFAULT 'new',
        notes TEXT,
        image_url VARCHAR(500),
        platform VARCHAR(100),
        url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->query($sql);

    // Create tags table
    $sql = "CREATE TABLE IF NOT EXISTS tags (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);

    // Create lead_tags table for many-to-many relationship
    $sql = "CREATE TABLE IF NOT EXISTS lead_tags (
        lead_id INT(11),
        tag_id INT(11),
        FOREIGN KEY (lead_id) REFERENCES leads(id),
        FOREIGN KEY (tag_id) REFERENCES tags(id),
        PRIMARY KEY (lead_id, tag_id)
    )";
    $conn->query($sql);
}
?>
