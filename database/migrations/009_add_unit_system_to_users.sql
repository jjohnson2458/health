ALTER TABLE users ADD COLUMN unit_system ENUM('us', 'metric') NOT NULL DEFAULT 'us' AFTER language;
