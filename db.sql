-- Create the database
CREATE DATABASE UserManagement;

-- Use the newly created database
USE UserManagement;


CREATE TABLE department (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(100) NOT NULL,
    head_user_id INT NOT NULl FOREIGN KEY REFERENCES users(id)
)

CREATE TABLE researchers (
    researcher_id INT AUTO_INCREMENT PRIMARY KEY,    
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,       
    country_code VARCHAR(5) DEFAULT '+65' NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    --> if can have like a dropdown list of all country codes for user to choose from 
    --> to implement in the html, can try useing Int-Tel-input library idk how it works tho so ,')
    --> else, keep to original  " phone_number VARCHAR(20) NOT NULL "
    password VARCHAR(255) NOT NULL,           
    role ENUM('Admin', 'Researcher', 'Assistant_Researcher') DEFAULT 'Assistant_Researcher' NOT NULL,
    department_id INT NULL FOREIGN KEY REFERENCES department(id)
)

CREATE TABLE equipment_inventory (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) NOT NULL, --> what dis?
    quantity INT NOT NULL, --> current stock level
    restock_level INT NOT NULL --> Min merch for said product
)

CREATE TABLE equipment_usage_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    equipment_id INT NOT NULL FOREIGN KEY REFERENCES equipment_inventory(equipment_id),
    last_used_by INT NOT NULL FOREIGN KEY REFERENCES researchers(id),
    department_id INT NOT NULL FOREIGN KEY REFERENCES department(id),
    quantity INT NOT NULL, --> How much of this equipment did the researcher take
)

CREATE TABLE project ( --> used to be reports
    project_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    title VARCHAR(255) NOT NULL,
    project_priority_level ENUM('Low', 'Medium', 'High') NOT NULL,
    generated_by INT NOT NULL FOREIGN KEY REFERENCES researchers(id),
    assigned_to INT NOT NULL FOREIGN KEY REFERENCES researchers(id),
    department_id INT NOT NULL FOREIGN KEY REFERENCES department(id),
    data_points TEXT NOT NULL, --> like assignment specs for the project
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
)

CREATE TABLE project_activity_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    project_id INT NOT NULL FOREIGN KEY REFERENCES project(project_id),
    project_status ENUM('Initialized', 'Ongoing', 'Completed') NOT NULL,
    last_updated_by INT NOT NULL FOREIGN KEY REFERENCES researchers(id),
    department_id INT NOT NULL FOREIGN KEY REFERENCES department(id),
    data_points TEXT NOT NULL, --> will display current progress of the project
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL --> last edited time lah or smth like that
)

--> all in one audit log? like for login attempts/new users/project creation deletion etc...?
CREATE TABLE audit_logs ( 
    log_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    researcher_id INT NOT NULL FOREIGN KEY REFERENCES researchers(id),
    audit_action TEXT NOT NULL,
    audit_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    details TEXT NOT NULL
)