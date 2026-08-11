-- =====================================================================
-- SERAPH BUILD CONSTRUCTION — Project details + layout file storage
-- Adds category, specs columns to projects and a project_layouts table
-- for downloadable layout files (PDF, images, any format).
-- Idempotent: safe to run multiple times.
-- =====================================================================

USE seraph_construction;

-- Add columns only if they don't exist yet (MariaDB 10.3+ / MySQL 8.0+)
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'category');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE projects ADD COLUMN category VARCHAR(100) NULL AFTER name', 'SELECT "category already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'plot_size');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE projects ADD COLUMN plot_size VARCHAR(100) NULL AFTER location', 'SELECT "plot_size already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'built_up_area');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE projects ADD COLUMN built_up_area VARCHAR(100) NULL AFTER plot_size', 'SELECT "built_up_area already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'floors');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE projects ADD COLUMN floors INT UNSIGNED NULL AFTER built_up_area', 'SELECT "floors already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'bedrooms');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE projects ADD COLUMN bedrooms INT UNSIGNED NULL AFTER floors', 'SELECT "bedrooms already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'bathrooms');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE projects ADD COLUMN bathrooms INT UNSIGNED NULL AFTER bedrooms', 'SELECT "bathrooms already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'style');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE projects ADD COLUMN style VARCHAR(100) NULL AFTER bathrooms', 'SELECT "style already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'thumbnail');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE projects ADD COLUMN thumbnail VARCHAR(500) NULL AFTER style', 'SELECT "thumbnail already exists" AS note');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS project_layouts (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id    INT UNSIGNED NOT NULL,
    filename      VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_type     VARCHAR(100) NOT NULL,
    file_size     INT UNSIGNED NOT NULL,
    file_data     LONGBLOB NOT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_layouts_project (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
