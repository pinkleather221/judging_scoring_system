-- Database Creation
CREATE DATABASE judge_scoring_system;
USE judge_scoring_system;

-- Judges Table
CREATE TABLE judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Users (Participants) Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Scores Table
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id INT NOT NULL,
    user_id INT NOT NULL,
    points INT NOT NULL CHECK (points >= 0 AND points <= 100),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_judge_user (judge_id, user_id)
);

-- Sample Data
INSERT INTO judges (username, display_name) VALUES
('judge1', 'Dr. Sarah Mitchell'),
('judge2', 'Prof. James Rodriguez'),
('judge3', 'Ms. Emily Chen');

INSERT INTO users (username, full_name, email) VALUES
('participant1', 'Alex Johnson', 'alex.johnson@email.com'),
('participant2', 'Maria Garcia', 'maria.garcia@email.com'),
('participant3', 'David Kim', 'david.kim@email.com'),
('participant4', 'Lisa Thompson', 'lisa.thompson@email.com'),
('participant5', 'Robert Wilson', 'robert.wilson@email.com');

-- Sample Scores
INSERT INTO scores (judge_id, user_id, points, comments) VALUES
(1, 1, 85, 'Excellent presentation skills'),
(1, 2, 92, 'Outstanding technical knowledge'),
(2, 1, 78, 'Good effort, needs improvement in delivery'),
(2, 3, 88, 'Very impressive work'),
(3, 2, 95, 'Exceptional performance');