-- =====================================================================
-- SERAPH BUILD CONSTRUCTION - Store photos in the database
-- New table to hold daily-update photos as binary blobs instead of the
-- uploads/ filesystem folder.
-- =====================================================================

USE seraph_construction;

CREATE TABLE update_images (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    update_id   INT UNSIGNED NOT NULL,
    file_name   VARCHAR(255) DEFAULT NULL,
    mime_type   VARCHAR(100) NOT NULL,
    size        INT UNSIGNED NOT NULL DEFAULT 0,
    data        LONGBLOB NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_update_images_update FOREIGN KEY (update_id)
        REFERENCES daily_updates(id) ON DELETE CASCADE,
    INDEX idx_update_images_update (update_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;