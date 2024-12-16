CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Researcher', 'Researcher Assistant') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Researchers (
    researcher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_info VARCHAR(255) NOT NULL,
    area_of_expertise VARCHAR(255),
    assigned_projects TEXT,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE SET NULL
);

CREATE TABLE ResearchProjects (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    funding DECIMAL(10, 2),
    team_members TEXT,
    status ENUM('Ongoing', 'Completed') DEFAULT 'Ongoing',
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES Users(user_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Equipment (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    usage_status VARCHAR(255),
    availability BOOLEAN DEFAULT TRUE,
    assigned_to INT,
    FOREIGN KEY (assigned_to) REFERENCES Users(user_id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    report_data TEXT,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES Users(user_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE PasswordResetRequests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    reset_token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Permissions (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('Admin', 'Researcher', 'Research Assistant') NOT NULL,
    permission VARCHAR(255) NOT NULL,
);