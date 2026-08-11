-- =====================================================================
-- SERAPH BUILD CONSTRUCTION — Project details + layout file storage
-- Adds category, specs columns to projects and a project_layouts table
-- for downloadable layout files (PDF, images, any format).
-- =====================================================================

USE seraph_construction;

ALTER TABLE projects
  ADD COLUMN category        VARCHAR(100) NULL AFTER name,
  ADD COLUMN plot_size        VARCHAR(100) NULL AFTER location,
  ADD COLUMN built_up_area    VARCHAR(100) NULL AFTER plot_size,
  ADD COLUMN floors           TINYINT UNSIGNED NULL AFTER built_up_area,
  ADD COLUMN bedrooms         TINYINT UNSIGNED NULL AFTER floors,
  ADD COLUMN bathrooms        TINYINT UNSIGNED NULL AFTER bedrooms,
  ADD COLUMN style            VARCHAR(100) NULL AFTER bathrooms,
  ADD COLUMN thumbnail        VARCHAR(500) NULL AFTER style;

CREATE TABLE project_layouts (
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
