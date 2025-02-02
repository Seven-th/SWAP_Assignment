<?php
$servername = "localhost";
$username = "root"; // or your database username
$password = ""; // or your database password
$dbname = "AMCSite";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS AMCSite";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database
if (!$conn->select_db($dbname)) {
    die("Error selecting database: " . $conn->error . "\n");
}

// User Table
$sql = "
CREATE TABLE IF NOT EXISTS user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,       
    phone_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Researcher', 'Research Assistant') NOT NULL DEFAULT 'Research Assistant',
    password_set BOOLEAN DEFAULT FALSE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'user' created successfully\n";
} else {
    echo "Error creating table 'user': " . $conn->error . "\n";
}

// Project Table
$sql = "
CREATE TABLE IF NOT EXISTS project (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    funding DECIMAL(10, 2),
    status ENUM('Ongoing', 'Completed'),
    project_priority_level ENUM('Low', 'Medium', 'High') NOT NULL,
    generated_by INT NOT NULL,
    assigned_to TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES user(user_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'project' created successfully\n";
} else {
    echo "Error creating table 'project': " . $conn->error . "\n";
}

// Equipment Inventory Table
$sql = "
CREATE TABLE IF NOT EXISTS equipment_inventory (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL, 
    restock_level INT NOT NULL 
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'equipment_inventory' created successfully\n";
} else {
    echo "Error creating table 'equipment_inventory': " . $conn->error . "\n";
}

// Report Table
$sql = "
CREATE TABLE IF NOT EXISTS reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    report_name VARCHAR(255) NOT NULL,
    report_type ENUM('Research Progress', 'Project Funding', 'Equipment Usage') NOT NULL,
    generated_by INT NOT NULL,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    report_data TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES user(user_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'reports' created successfully\n";
} else {
    echo "Error creating table 'reports': " . $conn->error . "\n";
}

// Password Reset Table
$sql = "
CREATE TABLE IF NOT EXISTS password_reset_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'password_reset_requests' created successfully\n";
} else {
    echo "Error creating table 'password_reset_requests': " . $conn->error . "\n";
}

// Insert sample users using prepared statements
$stmt = $conn->prepare("
INSERT INTO user (name, email, phone_number, password, role, password_set) 
VALUES (?, ?, ?, ?, ?, ?)
");
$name = 'a';
$email = 'a@amc.com';
$phone_number = '6581111111';
$password = 'password'; // In a real application, make sure to hash the password
$role = 'Admin';
$password_set = TRUE;

$stmt->bind_param("sssssi", $name, $email, $phone_number, $password, $role, $password_set);

if ($stmt->execute()) {
    echo "Sample user inserted successfully\n";
} else {
    echo "Error inserting user: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>