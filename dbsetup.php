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
$conn->select_db($dbname);

// Permission Table
$sql = "
CREATE TABLE IF NOT EXISTS permission (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('Admin', 'Researcher', 'Research Assistant') NOT NULL,
    permission VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'permission' created successfully\n";
} else {
    echo "Error creating table 'permission': " . $conn->error . "\n";
}

// Department Table
$sql = "
CREATE TABLE IF NOT EXISTS department (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'department' created successfully\n";
} else {
    echo "Error creating table 'department': " . $conn->error . "\n";
}

// Researcher Table
$sql = "
CREATE TABLE IF NOT EXISTS researcher (
    researcher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,       
    phone_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,           
    permission_id INT NOT NULL, 
    department_id INT NOT NULL, 
    FOREIGN KEY (permission_id) REFERENCES permission(permission_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'researcher' created successfully\n";
} else {
    echo "Error creating table 'researcher': " . $conn->error . "\n";
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

// Project Table
$sql = "
CREATE TABLE IF NOT EXISTS project (
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
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'project' created successfully\n";
} else {
    echo "Error creating table 'project': " . $conn->error . "\n";
}

// Password Reset Request Table
$sql = "
CREATE TABLE IF NOT EXISTS password_reset_request (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    researcher_id INT NOT NULL,
    FOREIGN KEY (researcher_id) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'password_reset_request' created successfully\n";
} else {
    echo "Error creating table 'password_reset_request': " . $conn->error . "\n";
}

// Project Activity Log Table
$sql = "
CREATE TABLE IF NOT EXISTS project_activity_log (
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
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'project_activity_log' created successfully\n";
} else {
    echo "Error creating table 'project_activity_log': " . $conn->error . "\n";
}

// Audit Logs Table
$sql = "
CREATE TABLE IF NOT EXISTS audit_logs ( 
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    researcher_id INT NOT NULL,
    audit_action TEXT NOT NULL,
    audit_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    details TEXT NOT NULL,
    FOREIGN KEY (researcher_id) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'audit_logs' created successfully\n";
} else {
    echo "Error creating table 'audit_logs': " . $conn->error . "\n";
}

// Equipment Usage Log Table
$sql = "
CREATE TABLE IF NOT EXISTS equipment_usage_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    last_used_by INT NOT NULL,
    department_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (equipment_id) REFERENCES equipment_inventory(equipment_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (last_used_by) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'equipment_usage_log' created successfully\n";
} else {
    echo "Error creating table 'equipment_usage_log': " . $conn->error . "\n";
}

// Insert sample values
$sql = "
INSERT INTO permission (role, permission) VALUES 
('Admin','Full access to all CRUD functions, including user management, project records, and equipment management.'),
('Researcher','Can create and manage their own research projects and equipment requests.'),
('Research Assistant','Can view assigned projects and update equipment usage.');
";
if ($conn->query($sql) === TRUE) {
    echo "Sample permissions inserted successfully\n";
} else {
    echo "Error inserting permissions: " . $conn->error . "\n";
}

$sql = "
INSERT INTO department (name) VALUES 
('Physics'),
('Chemistry'),
('Biology');
";
if ($conn->query($sql) === TRUE) {
    echo "Sample departments inserted successfully\n";
} else {
    echo "Error inserting departments: " . $conn->error . "\n";
}

$sql = "
INSERT INTO researcher (name, email, phone_number, password, permission_id, department_id) 
VALUES 
('john', 'john@gmail.com', '11112222', 'password', 1, 1),
('bob', 'bob@gmail.com', '33334444', 'password', 2, 2),
('Muthu', 'muthu@gmail.com', '55556666', 'password', 3, 3);
";
if ($conn->query($sql) === TRUE) {
    echo "Sample researchers inserted successfully\n";
} else {
    echo "Error inserting researchers: " . $conn->error . "\n";
}

$conn->close();
?>
