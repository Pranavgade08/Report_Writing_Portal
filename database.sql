CREATE DATABASE IF NOT EXISTS event_portal;
USE event_portal;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS academic_years (
  id INT AUTO_INCREMENT PRIMARY KEY,
  year_label VARCHAR(20) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  short_name VARCHAR(30) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  event_type ENUM('Seminar','Workshop','Sports','Cultural','Other') NOT NULL DEFAULT 'Other',
  event_date DATE NOT NULL,
  event_time TIME NULL,
  venue VARCHAR(150) NOT NULL,
  department_id INT NOT NULL,
  academic_year_id INT NOT NULL,
  organizer VARCHAR(150) NOT NULL,
  guest_speaker VARCHAR(150) NULL,
  participants_count INT NOT NULL DEFAULT 0,
  objectives TEXT NULL,
  outcomes TEXT NULL,
  description LONGTEXT NULL,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_events_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT,
  CONSTRAINT fk_events_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT,
  INDEX idx_events_date (event_date),
  INDEX idx_events_dept (department_id),
  INDEX idx_events_featured (featured),
  INDEX idx_events_type (event_type)
);

CREATE TABLE IF NOT EXISTS event_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  caption VARCHAR(255) NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_photos_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  INDEX idx_photos_event (event_id)
);

CREATE TABLE IF NOT EXISTS activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NOT NULL,
  action VARCHAR(50) NOT NULL,
  table_name VARCHAR(50) NOT NULL,
  record_id INT NOT NULL,
  description TEXT,
  ip_address VARCHAR(45),
  user_agent TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_logs_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

INSERT IGNORE INTO departments (name, short_name) VALUES
('BSC','BSC'),
('BCS','BCS'),
('Information Technology','IT'),
('Animation','ANIM'),
('BCA','BCA'),
('BBA','BBA'),
('BCOM','BCOM'),
('BSC Data Science','BSCDS'),
('MCA','MCA');

INSERT IGNORE INTO academic_years (year_label, is_active) VALUES
('2024-25',1),
('2025-26',1);

INSERT IGNORE INTO admins (username, password_hash, full_name)
VALUES (
  'admin',
  '$2y$10$5nzSCukgZSgLVHRHIjquV.gaB7cFY/UpLEsMS2f5gsqFk8aXzORc2',
  'Faculty Admin'
);

-- Safe migration for existing databases that may be missing the `featured` column
ALTER TABLE events
  ADD COLUMN IF NOT EXISTS featured TINYINT(1) NOT NULL DEFAULT 0,
  ADD INDEX IF NOT EXISTS idx_events_featured (featured);
