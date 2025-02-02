-- Create the database
CREATE DATABASE AMCSite;
USE AMCSite;

-- User Table (Group and Part 1)
CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,       
    phone_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Researcher', 'Research Assistant') NOT NULL DEFAULT 'Research Assistant',
    password_set BOOLEAN DEFAULT FALSE
);

-- Project Table (Part 2)
CREATE TABLE project (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    funding DECIMAL(10, 2),
    status ENUM('Ongoing', 'Completed') DEFAULT 'Ongoing',
    project_priority_level ENUM('Low', 'Medium', 'High') NOT NULL,
    assigned_to TEXT NOT NULL
);

-- Equipment Inventory Table (Part 3)
CREATE TABLE equipment_inventory (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL, 
    restock_level INT NOT NULL 
);

-- Report Table (Part 4)
CREATE TABLE reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    report_name VARCHAR(255) NOT NULL,
    report_type ENUM('Research Progress', 'Project Funding', 'Equipment Usage') NOT NULL,
    generated_by INT NOT NULL,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    report_data TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES user(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);
-- Password reset request table (Group Component
CREATE TABLE password_reset_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);


-- Insert Users
INSERT INTO user (name, email, phone_number, password, role, password_set) 
VALUES ('a', 'a@amc.com', '6581111111', 'password', 'Admin', TRUE);