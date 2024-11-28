-- Create the database
CREATE DATABASE UserManagement;

-- Use the newly created database
USE UserManagement;


CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,    
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,       
    password VARCHAR(255) NOT NULL,           
    role ENUM('admin', 'researcher', 'Assistant_Researcher') DEFAULT 'Assistant_Researcher' NOT NULL,
    department_id INT NULL FOREIGN KEY REFERENCES department(id)
);

CREATE TABLE department (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(100) NOT NULL,
    head_user_id INT NOT NULl FOREIGN KEY REFERENCES users(id)
)

CREATE TABLE vendors (
    vendor_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(255) NOT NULL,
    country_code VARCHAR(5) DEFAULT '+65' NOT NULL, 
    --> if can have like a dropdown list of all country codes for user to choose from 
    --> to implement in the html, can try useing Int-Tel-input library idk how it works tho so ,')
    --> else, keep to original  " phone_number VARCHAR(20) NOT NULL "
    phone_number VARCHAR(15) NOT NULL,  
    services_provided TEXT NOT NULL,
    payment_terms VARCHAR(255) NOT NULL
)

CREATE TABLE procurement_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    priority_level ENUM('Low', 'Medium', 'High') NOT NULL,
    department_id INT NOT NULL FOREIGN KEY REFERENCES department(name), --> id or username better?
    status ENUM('Pending', 'Approved', 'Rejected'),
    created_by INT NOT NULL FOREIGN KEY REFERENCES users(id) --> id or username better?
)

CREATE TABLE purchase_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    vendor_id INT NOT NULL FOREIGN KEY REFERENCES vendors(id),
    request_id INT NOT NULL FOREIGN KEY REFERENCES procurement_requests(id),
    status ENUM('Pending', 'Approved', 'Completed') NOT NULL,
    assigned_to INT NOT NULL FOREIGN KEY REFERENCES users(id),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
)

CREATE TABLE equipment_inventory(
    inventory_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    restock_level INT NOT NULL
)