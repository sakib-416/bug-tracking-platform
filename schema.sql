-- schema.sql
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user',
    avatar TEXT DEFAULT 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
    bio TEXT
);

DROP TABLE IF EXISTS issues;
CREATE TABLE issues (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    severity TEXT CHECK(severity IN ('Low', 'Medium', 'High', 'Critical')) DEFAULT 'Medium',
    status TEXT CHECK(status IN ('To Do', 'In Progress', 'Testing', 'Completed')) DEFAULT 'To Do',
    reporter_id INTEGER,
    assignee_id INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(reporter_id) REFERENCES users(id),
    FOREIGN KEY(assignee_id) REFERENCES users(id)
);

DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    issue_id INTEGER,
    user_id INTEGER,
    comment_text TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(issue_id) REFERENCES issues(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);

-- Seed Data
INSERT INTO users (username, password, role, avatar, bio) VALUES 
('admin', 'admin123', 'admin', 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=100&auto=format&fit=crop&q=80', 'System Administrator & Security Lead'),
('developer_alice', 'alice99', 'user', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80', 'Frontend UI Engineer'),
('attacker', 'attacker', 'user', 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&auto=format&fit=crop&q=80', 'Security Research Sandbox Account');

INSERT INTO issues (title, description, severity, status, reporter_id, assignee_id) VALUES 
('SQLi vulnerability in search bar', 'The lookup mechanism is parsing inputs unescaped.', 'Critical', 'In Progress', 2, 1),
('Fix layout broken on mobile devices', 'The navigation bar overflows on screen sizes under 375px wide.', 'Low', 'To Do', 1, 2),
('Session tokens lack HTTPOnly flag', 'Audit shows session cookies can be accessed via scripts.', 'High', 'Testing', 2, 1),
('Refactor routing matrix optimization', 'Need to decouple production dependency routing trees.', 'Medium', 'Completed', 1, 2);

INSERT INTO comments (issue_id, user_id, comment_text) VALUES 
(1, 1, 'Working on reproducing this inside our testing containers now.'),
(1, 2, 'Ensure you isolate the DB layer when verification begins.');
