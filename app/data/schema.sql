-- Loading test data for employees table 
-- Used only with sqlite3 for testing purposes
-- $ sqlite3 urbdyn.db < schema.sql

CREATE TABLE employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(32) NOT NULL,
    email VARCHAR(128) NOT NULL,
    phone VARCHAR(16) NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO employees (name, email, phone) 
VALUES 
('Freddy Giordano', 'freddy@megacorp.com', '6027503733'),
('Sarah Johnson', 'sarah.johnson@megacorp.com', '4805551001'),
('Michael Smith', 'michael.smith@megacorp.com', '4805551002'),
('Jennifer Davis', 'jennifer.davis@megacorp.com', '4805551003'),
('Robert Wilson', 'robert.wilson@megacorp.com', '4805551004'),
('Emily Brown', 'emily.brown@megacorp.com', '4805551005'),
('David Martinez', 'david.martinez@megacorp.com', '4805551006'),
('Jessica Anderson', 'jessica.anderson@megacorp.com', '4805551007'),
('Christopher Taylor', 'christopher.taylor@megacorp.com', '4805551008'),
('Amanda Thompson', 'amanda.thompson@megacorp.com', '4805551009');

CREATE TABLE lunches (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    requester_id INTEGER NOT NULL,
    recipient_id INTEGER NOT NULL,
    date DATETIME NOT NULL,
    deleted DATETIME NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated DATETIME DEFAULT CURRENT_TIMESTAMP
);