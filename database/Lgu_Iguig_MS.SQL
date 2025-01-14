-- Roles Table
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);


-- Insert a role
INSERT INTO roles (role_name, description)
VALUES ('Admin', 'Administrator role for managing system operations'),
('Employee', 'Administrator role for managing system operations');  

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- Insert a user (assuming role_id 1 exists in the roles table)
INSERT INTO users (username, password, email, first_name, last_name, role_id)
VALUES 
    ('admin', SHA2('admin123', 256), 'admin@example.com', 'Admin', 'User', 1);


CREATE TABLE departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Insert departments
INSERT INTO departments (department_name, description)
VALUES 
    ('Finance', 'Handles all financial activities including budgeting and accounting.'),
    ('Human Resources', 'Manages employee relations, payroll, and recruitment.'),
    ('IT Department', 'Responsible for managing the company’s technology infrastructure.');

-- Employee Details Table
CREATE TABLE employee_details (
    employee_id INT PRIMARY KEY,
    department_id INT,
    designation VARCHAR(100),
    hire_date DATE,
    office_location VARCHAR(100),
    FOREIGN KEY (employee_id) REFERENCES users(user_id),
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);

-- Insert employee details
INSERT INTO employee_details (employee_id, department_id, designation, hire_date, office_location)
VALUES
    (1, 1, 'Chief Financial Officer', '2020-01-15', 'Finance Office');



-- Permissions Table
CREATE TABLE permissions (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Insert permissions
INSERT INTO permissions (permission_name, description)
VALUES 
    ('View Documents', 'Allows the user to view documents in the system'),
    ('Edit Documents', 'Allows the user to edit existing documents'),
    ('Approve Documents', 'Grants the user the ability to approve documents'),
    ('Create Users', 'Allows the user to create new user accounts'),
    ('Delete Documents', 'Allows the user to delete documents from the system');

-- Role Permissions Table
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id),
    PRIMARY KEY (role_id, permission_id)
);

-- Insert role permissions
INSERT INTO role_permissions (role_id, permission_id)
VALUES
    (1, 1),  
    (1, 2),  
    (1, 3),  
    (1, 4),  
    (1, 5);  


-- Position Table 
CREATE TABLE positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(255) NOT NULL UNIQUE
);

-- Insert a position
INSERT INTO positions (position_name) 
VALUES ('Manager');

-- Document Table
CREATE TABLE Document (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_type ENUM('Memorandum', 'Resolution', 'Ordinance') NOT NULL,
    document_id VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    authored_by INT NOT NULL,
    date DATE NOT NULL,
    subject TEXT NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    rejection_reason TEXT,
    is_archived BOOLEAN DEFAULT FALSE,
    file_path VARCHAR(255),  -- New column for storing the file path
    FOREIGN KEY (authored_by) REFERENCES users(user_id) ON DELETE CASCADE
);



INSERT INTO Document (document_type, document_id, title, authored_by, date, subject, description, status)
VALUES 
    ('Memorandum', 'DOC001', 'New Memorandum', 1, '2025-01-13', 'Subject 1', 'Description of the document', 'Pending');


CREATE TABLE DocumentFiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id VARCHAR(100) NOT NULL,
    file_type ENUM('pdf', 'docx', 'jpg', 'png', 'xlsx') NOT NULL,  
    file_path VARCHAR(255) NOT NULL,  
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,  
    uploaded_by INT NOT NULL, 
    FOREIGN KEY (document_id) REFERENCES Document(document_id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id) ON DELETE CASCADE
);


-- Archived Documents Table
CREATE TABLE ArchivedDocuments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    document_type ENUM('Memorandum', 'Resolution', 'Ordinance') NOT NULL,
    authored_by INT NOT NULL,
    date DATE NOT NULL,
    subject TEXT NOT NULL,
    description TEXT NOT NULL,
    rejection_reason TEXT,
    archived_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (authored_by) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Event Table
CREATE TABLE Event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    approved_by INT,
    event_type ENUM('Cultural', 'Special Session', 'Governmental') NOT NULL,
    is_archived BOOLEAN DEFAULT FALSE,
    archival_reason TEXT,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Insert an event
INSERT INTO Event (name, date, event_type, approved_by)
VALUES 
    ('New Event', '2025-01-15', 'Cultural', 1);

-- Document Tracking Table
CREATE TABLE DocumentTracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id VARCHAR(100) NOT NULL,
    related_event_id INT,
    created_by INT NOT NULL,
    updated_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (document_id) REFERENCES Document(document_id) ON DELETE CASCADE,
    FOREIGN KEY (related_event_id) REFERENCES Event(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert a document tracking record
INSERT INTO DocumentTracking (document_id, related_event_id, created_by, updated_by)
VALUES 
    ('DOC001', 1, 1, 1);
-- Admin Logs Table
CREATE TABLE AdminLogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action_type ENUM('Add', 'Edit', 'Delete', 'Approve', 'Reject') NOT NULL,
    target_table ENUM('Document', 'Event', 'User') NOT NULL,
    target_id INT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(user_id)
);

-- Insert admin log
INSERT INTO AdminLogs (admin_id, action_type, target_table, target_id)
VALUES 
    (1, 'Add', 'Document', 1);

-- Email Verification Table
CREATE TABLE EmailVerification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type_id INT NOT NULL,
    verification_code VARCHAR(255) NOT NULL,
    expiry_date DATETIME NOT NULL,
    FOREIGN KEY (user_type_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert email verification
INSERT INTO EmailVerification (user_type_id, verification_code, expiry_date)
VALUES 
    (1, 'abc123', '2025-02-13');

-- Notification Table
CREATE TABLE Notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type_id INT NOT NULL,
    message TEXT NOT NULL,
    notification_type ENUM('Download', 'StatusUpdate') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_type_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert notification
INSERT INTO Notification (user_type_id, message, notification_type)
VALUES 
    (1, 'Document approved.', 'StatusUpdate');

-- Carousel Content Table
CREATE TABLE CarouselContent (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    caption TEXT,
    link VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert carousel content
INSERT INTO CarouselContent (image, caption, link)
VALUES 
    ('image_path.jpg', 'Image caption', 'http://example.com');

-- LGU Details Table
CREATE TABLE LGUDetails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    history TEXT NOT NULL,
    hotline VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    mission TEXT NOT NULL,
    vision TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert LGU details
INSERT INTO LGUDetails (history, hotline, email, address, mission, vision)
VALUES 
    ('LGU History', '123-4567', 'lgu@example.com', '123 LGU St.', 'Serve the people', 'Be the best');

-- Document Timeline Table
CREATE TABLE DocumentTimeline (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id VARCHAR(100) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL,
    status_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    action_by INT NOT NULL,  
    action_reason TEXT,
    FOREIGN KEY (document_id) REFERENCES Document(document_id) ON DELETE CASCADE,
    FOREIGN KEY (action_by) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert document timeline
INSERT INTO DocumentTimeline (document_id, status, action_by, action_reason)
VALUES 
    ('DOC001', 'Approved', 1, 'Document approved by admin');