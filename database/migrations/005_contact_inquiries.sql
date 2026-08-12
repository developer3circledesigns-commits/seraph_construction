-- =====================================================================
-- SERAPH BUILD CONSTRUCTION — Public contact / quote inquiries
-- =====================================================================

USE seraph_construction;

CREATE TABLE IF NOT EXISTS contact_inquiries (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(120) NOT NULL,
    email           VARCHAR(255) NOT NULL,
    phone           VARCHAR(30) NOT NULL,
    service_type    VARCHAR(80) DEFAULT NULL,
    message         TEXT NOT NULL,
    ip_address      VARCHAR(45) DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_created (created_at),
    INDEX idx_contact_ip (ip_address, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
