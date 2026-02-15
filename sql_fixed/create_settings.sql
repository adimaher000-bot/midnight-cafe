-- sql_fixed/create_settings.sql
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT
);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Midnight Cafe'),
('hero_title', 'Welcome to Midnight Cafe'),
('hero_subtitle', 'Experience the taste of cyber-botanic fusion.'),
('footer_text', 'Midnight Cyber Cafe'),
('social_facebook', '#'),
('social_instagram', '#'),
('featured_item_id', '1')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
