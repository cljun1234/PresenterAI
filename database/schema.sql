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

-- Coupons Table
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_id INT NOT NULL,
    title VARCHAR(255) DEFAULT 'Special Offer',
    description TEXT,
    coupon_code VARCHAR(100) NOT NULL,
    button_text VARCHAR(100) DEFAULT 'Copy Code',
    bg_color VARCHAR(50) DEFAULT '#ffffff',
    text_color VARCHAR(50) DEFAULT '#333333',
    trigger_type VARCHAR(50) DEFAULT 'delay', -- 'delay' or 'exit_intent'
    trigger_delay INT DEFAULT 0, -- Seconds
    frequency VARCHAR(50) DEFAULT 'every_load', -- 'every_load' or 'session'
    match_url VARCHAR(255) DEFAULT NULL, -- URL pattern to match, NULL = all
    active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image_url VARCHAR(255) DEFAULT NULL,
    image_style VARCHAR(50) DEFAULT 'top', -- 'top', 'left', 'right', 'background'
    remove_branding BOOLEAN DEFAULT 0,
    FOREIGN KEY (widget_id) REFERENCES widgets(id) ON DELETE CASCADE
);

-- Coupon Analytics
CREATE TABLE IF NOT EXISTS coupon_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT NOT NULL,
    event_type VARCHAR(50) NOT NULL, -- 'view', 'click'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE
);

-- Announcements Table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_id INT NOT NULL,
    title VARCHAR(255) DEFAULT 'Announcement',
    message TEXT,
    image_url VARCHAR(255) DEFAULT NULL,
    image_style VARCHAR(50) DEFAULT 'top', -- 'top', 'left', 'right', 'background'
    btn_text VARCHAR(100) DEFAULT 'Learn More',
    btn_action VARCHAR(50) DEFAULT 'link', -- 'link' or 'close'
    btn_link VARCHAR(255) DEFAULT NULL,
    remove_branding BOOLEAN DEFAULT 0,
    bg_color VARCHAR(50) DEFAULT '#ffffff',
    text_color VARCHAR(50) DEFAULT '#333333',
    trigger_type VARCHAR(50) DEFAULT 'delay',
    trigger_delay INT DEFAULT 0,
    frequency VARCHAR(50) DEFAULT 'every_load',
    match_url VARCHAR(255) DEFAULT NULL,
    active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (widget_id) REFERENCES widgets(id) ON DELETE CASCADE
);

-- Announcement Analytics
CREATE TABLE IF NOT EXISTS announcement_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    event_type VARCHAR(50) NOT NULL, -- 'view', 'click'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE
);
