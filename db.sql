-- Create the database
CREATE DATABASE UserManagement;

-- Use the newly created database
USE UserManagement;




-- Permission Table
CREATE TABLE permission (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('Admin', 'Researcher', 'Research Assistant') NOT NULL,
    permission VARCHAR(255) NOT NULL
);

-- Department Table
CREATE TABLE department (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Researcher Table
CREATE TABLE researcher (
    researcher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,       
    phone_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,           
    permission_id INT NOT NULL, 
    department_id INT NOT NULL, 
    FOREIGN KEY (permission_id) REFERENCES permission(permission_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Equipment Inventory Table
CREATE TABLE equipment_inventory (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL, 
    restock_level INT NOT NULL 
);

-- Project Table
CREATE TABLE project (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    funding DECIMAL(10, 2),
    status ENUM('Ongoing', 'Completed') DEFAULT 'Ongoing',
    project_priority_level ENUM('Low', 'Medium', 'High') NOT NULL,
    generated_by INT NOT NULL,
    assigned_to INT NOT NULL,
    department_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Password Reset Request Table
CREATE TABLE password_reset_request (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    researcher_id INT NOT NULL,
    FOREIGN KEY (researcher_id) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Project Activity Log Table
CREATE TABLE project_activity_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    project_status ENUM('Ongoing', 'Completed') NOT NULL DEFAULT 'Ongoing',
    last_updated_by INT NOT NULL,
    department_id INT NOT NULL,
    data_points TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, 
    FOREIGN KEY (project_id) REFERENCES project(project_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (last_updated_by) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Audit Logs Table
CREATE TABLE audit_logs ( 
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    researcher_id INT NOT NULL,
    audit_action TEXT NOT NULL,
    audit_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    details TEXT NOT NULL,
    FOREIGN KEY (researcher_id) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Equipment Usage Log Table
CREATE TABLE equipment_usage_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    last_used_by INT NOT NULL,
    department_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (equipment_id) REFERENCES equipment_inventory(equipment_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (last_used_by) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- INSERT VALUES INTO TABLE
-- FOR PERMISSIONS TABLE
INSERT INTO permission (role, permission) VALUES ('Admin','Full access to all CRUD functions, including user management, project records, and equipment management.');
INSERT INTO permission (role, permission) VALUES ('Researcher','Can create and manage their own research projects and equipment requests.');
INSERT INTO permission (role, permission) VALUES ('Research Assistant','Can view assigned projects and update equipment usage.');

-- FOR DEPARTMENT TABLE
INSERT INTO department (name) VALUES ('Physics');
INSERT INTO department (name) VALUES ('Chemistry');
INSERT INTO department (name) VALUES ('Biology');

-- FOR USERS TABLE i.e. CREATE users to test login / forgot pass functions
INSERT INTO researcher (name, email, phone_number, password, permission_id, department_id) 
VALUES ('john', 'john@gmail.com', '11112222', 'password', '1', '1');
INSERT INTO researcher (name, email, phone_number, password, permission_id, department_id) 
VALUES ('bob', 'bob@gmail.com', '33334444', 'password', '2', '2');
INSERT INTO researcher (name, email, phone_number, password, permission_id, department_id) 
VALUES ('Muthu', 'muthu@gmail.com', '55556666', 'password', '3', '3')