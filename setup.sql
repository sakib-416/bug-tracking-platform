CREATE DATABASE IF NOT EXISTS sqli;
USE sqli;

CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    designation VARCHAR(255),
    image VARCHAR(500),
    department VARCHAR(100) DEFAULT 'Engineering',
    severity VARCHAR(50) DEFAULT 'medium',
    status VARCHAR(50) DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bugs_stored (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    reported_by VARCHAR(255),
    severity VARCHAR(50) DEFAULT 'medium',
    status VARCHAR(50) DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO faculty (name, designation, image, department, severity, status) VALUES
('Auth Bypass via JWT None Algorithm', 'Authentication tokens accept "none" as a valid algorithm, allowing unsigned tokens.', 'CVE-2024-0001', 'Security', 'critical', 'open'),
('Remote Code Execution in File Upload', 'Unrestricted file upload allows .php shell execution via /uploads endpoint.', 'CVE-2024-0002', 'Backend', 'critical', 'open'),
('Insecure Direct Object Reference', 'User IDs exposed in API /api/user/{id} without authorization checks.', 'CVE-2024-0003', 'API', 'high', 'open'),
('CSRF on Password Reset Endpoint', 'Password reset form lacks CSRF token, enabling cross-site request forgery.', 'CVE-2024-0004', 'Frontend', 'high', 'open'),
('Dependency: lodash Prototype Pollution', 'lodash@4.17.15 vulnerable to prototype pollution via merge/set functions.', 'CVE-2024-0005', 'Dependencies', 'medium', 'open');

INSERT INTO bugs_stored (title, description, reported_by, severity, status) VALUES
('Initial seed: login page title', 'Welcome to BugVault — Bug Tracking System', 'system', 'low', 'open');
