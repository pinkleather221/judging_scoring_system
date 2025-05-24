# LAMP Stack Judge Scoring System

A comprehensive web application built on the LAMP stack for managing judges, participants, and scoring in competitions or events.

##  Features

- **Admin Panel**: Add and manage judges and participants
- **Judge Portal**: Interactive scoring interface with real-time validation
- **Public Scoreboard**: Live, auto-refreshing leaderboard with animations
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Real-time Updates**: Automatic scoreboard refresh every 30 seconds
- **Data Validation**: Comprehensive form validation and error handling
- **Modern UI**: Bootstrap 5 with custom CSS animations and effects

##  Requirements

- **Apache**: Web server with mod_rewrite enabled
- **MySQL**: Database server (5.7+ recommended)
- **PHP**: Version 7.4 or higher with PDO extension
- **Browser**: Modern web browser with JavaScript enabled

##  Installation

# Option 1: XAMPP (Recommended for local development)

1. **Download and Install XAMPP**
   ```bash
   # Download from https://www.apachefriends.org/
   # Install and start Apache and MySQL services

Clone/Download Project
bashcd /path/to/xampp/htdocs/
git clone  https://github.com/pinkleather221/judging_scoring_system.git
# Or extract downloaded files to this directory

Database Setup
bash# Open phpMyAdmin (http://localhost/phpmyadmin)
# Create new database: judge_scoring_system
# Import the SQL schema provided below

Configuration
php// Edit config/database.php if needed
private $host = 'localhost';
private $db_name = 'judge_scoring_system';
private $username = 'root';
private $password = ''; // Usually empty for XAMPP

Access Application
Main Scoreboard: http://localhost/judge-scoring-system/
Admin Panel: http://localhost/judge-scoring-system/admin/
Judge Portal: http://localhost/judge-scoring-system/judge/


Option 2: Docker LAMP Stack
yaml# docker-compose.yml
version: '3.8'
services:
  web:
    image: php:8.1-apache
    ports:
      - "80:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
  
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: judge_scoring_system
    ports:
      - "3306:3306"
 Database Schema
The system uses three main tables with proper relationships and constraints:
Tables Structure

judges: Stores judge information

id (Primary Key, Auto-increment)
username (Unique identifier)
display_name (Full name for display)
created_at (Timestamp)
is_active (Boolean status)


users: Stores participant information

id (Primary Key, Auto-increment)
username (Unique identifier)
full_name (Participant's full name)
email (Optional contact email)
registration_date (Timestamp)
is_active (Boolean status)


scores: Stores scoring data

id (Primary Key, Auto-increment)
judge_id (Foreign Key to judges)
user_id (Foreign Key to users)
points (Score value, 0-100)
comments (Optional feedback)
created_at & updated_at (Timestamps)
Unique constraint on (judge_id, user_id)



Sample Data Included

3 Sample judges with different professional titles
5 Sample participants with varied profiles
Initial scoring data to demonstrate functionality

Core Functionality
Admin Panel (/admin/)

Judge Management: Add new judges with username and display name
Participant Management: Add participants with full details
System Statistics: View counts and averages
Data Validation: Prevents duplicate usernames
Real-time Feedback: Success/error messages for all operations

Judge Portal (/judge/)

Judge Selection: Choose from active judges
Participant Scoring: Interactive scoring with 0-100 range
Score Validation: Real-time input validation and descriptions
Previous Scores: View judge's scoring history
Comments System: Add detailed feedback for participants
Update Capability: Modify existing scores

Public Scoreboard (/)

Live Rankings: Sorted by total points (descending)
Auto-refresh: Updates every 30 seconds
Visual Indicators: Animated rank badges and progress bars
Judge Count: Shows how many judges scored each participant
Responsive Design: Optimized for all screen sizes
Loading States: Visual feedback during updates

Technical Implementation
Backend (PHP)

PDO Database Layer: Secure, prepared statements
Error Handling: Comprehensive exception catching
Data Validation: Server-side input validation
RESTful APIs: JSON endpoints for dynamic updates
Security: SQL injection prevention, XSS protection

Frontend (HTML/CSS/JS)

Bootstrap 5: Responsive framework
Custom CSS: Advanced animations and effects
Vanilla JavaScript: No external dependencies
AJAX: Asynchronous data loading
Progressive Enhancement: Works without JS

Database Design

Normalized Schema: Efficient data structure
Foreign Key Constraints: Data integrity
Indexed Columns: Optimized queries
Transaction Support: Data consistency

## Unique Features

Animated Scoreboard: Smooth transitions and visual effects
Score Range Slider: Interactive point selection
Real-time Descriptions: Dynamic score quality indicators
Glassmorphism Design: Modern UI with backdrop blur effects
Progressive Web App Ready: Responsive and mobile-optimized
Comprehensive Statistics: Admin dashboard with key metrics
Duplicate Prevention: Smart handling of existing scores
