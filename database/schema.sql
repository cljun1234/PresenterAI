-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Widgets (Configuration for a domain)
CREATE TABLE IF NOT EXISTS widgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    domain VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    magical_detection BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications (Simulated data)
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    action_text VARCHAR(255) NOT NULL,
    location VARCHAR(255) DEFAULT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (widget_id) REFERENCES widgets(id) ON DELETE CASCADE
);

-- Live Visitors (Heartbeats)
CREATE TABLE IF NOT EXISTS live_visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_id INT NOT NULL,
    visitor_id VARCHAR(255) NOT NULL,
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    url VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (widget_id) REFERENCES widgets(id) ON DELETE CASCADE,
    INDEX (last_seen)
);

-- Events (Real Conversions)
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_id INT NOT NULL,
    type VARCHAR(50) NOT NULL, -- 'form_submit', etc.
    payload TEXT DEFAULT NULL, -- JSON data
    visitor_id VARCHAR(255) DEFAULT NULL,
    page_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (widget_id) REFERENCES widgets(id) ON DELETE CASCADE
);
-- Add Traffic Snapshots table
CREATE TABLE IF NOT EXISTS traffic_snapshots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_id INT NOT NULL,
    visitor_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (widget_id) REFERENCES widgets(id) ON DELETE CASCADE
);

-- Add Live Visitor Configuration to widgets
ALTER TABLE widgets ADD COLUMN live_visitor_enabled BOOLEAN DEFAULT 0;
ALTER TABLE widgets ADD COLUMN live_visitor_config TEXT DEFAULT NULL; -- JSON string for styles

-- Live Conversion Settings
ALTER TABLE widgets ADD COLUMN live_conversion_enabled BOOLEAN DEFAULT 0;
ALTER TABLE widgets ADD COLUMN use_real_conversion BOOLEAN DEFAULT 1;
ALTER TABLE widgets ADD COLUMN use_simulated_conversion BOOLEAN DEFAULT 1;
-- Add Timezone to widgets
ALTER TABLE widgets ADD COLUMN timezone VARCHAR(50) DEFAULT 'UTC';
