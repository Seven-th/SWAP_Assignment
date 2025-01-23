<?php
$host = 'localhost';  // Your MySQL host (usually 'localhost')
$username = 'root';   // Your MySQL username
$password = '';       // Your MySQL password
$dbname = 'UserManagement'; // The database name

// Establishing a connection to the MySQL database
$conn = new mysqli($host, $username, $password);

// Check for a connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' created or already exists.\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database
$conn->select_db($dbname);

// Create tables

// Permission Table
$sql = "CREATE TABLE IF NOT EXISTS permission (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('Admin', 'Researcher', 'Research Assistant') NOT NULL,
    permission VARCHAR(255) NOT NULL
)";
$conn->query($sql);

// Department Table
$sql = "CREATE TABLE IF NOT EXISTS department (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
$conn->query($sql);

// Researcher Table
$sql = "CREATE TABLE IF NOT EXISTS researcher (
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
$conn->query($sql);

// Equipment Inventory Table
$sql = "CREATE TABLE IF NOT EXISTS equipment_inventory (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL, 
    restock_level INT NOT NULL 
)";
$conn->query($sql);

// Project Table
$sql = "CREATE TABLE IF NOT EXISTS project (
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
$conn->query($sql);

// Password Reset Request Table
$sql = "CREATE TABLE IF NOT EXISTS password_reset_request (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    researcher_id INT NOT NULL,
    FOREIGN KEY (researcher_id) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
$conn->query($sql);

// Project Activity Log Table
$sql = "CREATE TABLE IF NOT EXISTS project_activity_log (
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
$conn->query($sql);

// Audit Logs Table
$sql = "CREATE TABLE IF NOT EXISTS audit_logs ( 
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    researcher_id INT NOT NULL,
    audit_action TEXT NOT NULL,
    audit_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    details TEXT NOT NULL,
    FOREIGN KEY (researcher_id) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
$conn->query($sql);

// Equipment Usage Log Table
$sql = "CREATE TABLE IF NOT EXISTS equipment_usage_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    last_used_by INT NOT NULL,
    department_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (equipment_id) REFERENCES equipment_inventory(equipment_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (last_used_by) REFERENCES researcher(researcher_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE CASCADE ON UPDATE CASCADE
)";
$conn->query($sql);

// Insert values into tables

// Insert into permission table
$sql = "INSERT INTO permission (role, permission) VALUES
    ('Admin','Full access to all CRUD functions, including user management, project records, and equipment management.'),
    ('Researcher','Can create and manage their own research projects and equipment requests.'),
    ('Research Assistant','Can view assigned projects and update equipment usage.')";
$conn->query($sql);

// Insert into department table
$sql = "INSERT INTO department (name) VALUES
    ('Physics'),
    ('Chemistry'),
    ('Biology')";
$conn->query($sql);

// Insert into researcher table
$sql = "INSERT INTO researcher (name, email, phone_number, password, permission_id, department_id) VALUES
    ('john', 'john@gmail.com', '11112222', 'password', 1, 1),
    ('bob', 'bob@gmail.com', '33334444', 'password', 2, 2),
    ('Muthu', 'muthu@gmail.com', '55556666', 'password', 3, 3)";
$conn->query($sql);

echo "Database setup completed successfully!";

// Close the connection
$conn->close();
?>
