-- =====================================================================
-- SERAPH BUILD CONSTRUCTION — Initial schema
-- Admin + Client portal database for project status tracking
-- =====================================================================

CREATE DATABASE IF NOT EXISTS seraph_construction
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE seraph_construction;

-- ---------------------------------------------------------------------
-- Admins — the construction company staff. N admins supported.
-- role: super_admin (full control) | admin (manages assigned projects)
-- ---------------------------------------------------------------------
CREATE TABLE admins (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    full_name       VARCHAR(120) NOT NULL,
    phone           VARCHAR(30) DEFAULT NULL,
    role            ENUM('super_admin','admin') NOT NULL DEFAULT 'admin',
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at   DATETIME DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Clients — the construction customers. N clients supported.
-- ---------------------------------------------------------------------
CREATE TABLE clients (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    company_name    VARCHAR(200) DEFAULT NULL,
    contact_person  VARCHAR(120) NOT NULL,
    phone           VARCHAR(30) DEFAULT NULL,
    address         TEXT,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at   DATETIME DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Projects — one client can have many projects.
-- ---------------------------------------------------------------------
CREATE TABLE projects (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id           INT UNSIGNED NOT NULL,
    name                VARCHAR(200) NOT NULL,
    description         TEXT,
    location            VARCHAR(500) DEFAULT NULL,
    start_date          DATE DEFAULT NULL,
    estimated_end_date  DATE DEFAULT NULL,
    actual_end_date     DATE DEFAULT NULL,
    status              ENUM('planning','in_progress','on_hold','completed','cancelled')
                        NOT NULL DEFAULT 'planning',
    progress_percentage TINYINT UNSIGNED NOT NULL DEFAULT 0,
    budget              DECIMAL(15,2) DEFAULT NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_projects_client FOREIGN KEY (client_id)
        REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_projects_client (client_id),
    INDEX idx_projects_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Daily updates — the core feature. One per project per calendar day.
-- ---------------------------------------------------------------------
CREATE TABLE daily_updates (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id          INT UNSIGNED NOT NULL,
    admin_id            INT UNSIGNED NOT NULL,
    update_date         DATE NOT NULL,
    status              ENUM('planning','in_progress','on_hold','completed','cancelled')
                        NOT NULL DEFAULT 'in_progress',
    progress_percentage TINYINT UNSIGNED NOT NULL DEFAULT 0,
    title               VARCHAR(255) NOT NULL,
    description         TEXT,
    images              JSON DEFAULT NULL,
    materials_used      TEXT,
    labor_count         SMALLINT UNSIGNED DEFAULT NULL,
    weather_condition   VARCHAR(50) DEFAULT NULL,
    next_day_plan       TEXT,
    is_milestone        TINYINT(1) NOT NULL DEFAULT 0,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_updates_project FOREIGN KEY (project_id)
        REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_updates_admin FOREIGN KEY (admin_id)
        REFERENCES admins(id),
    UNIQUE KEY uq_project_date (project_id, update_date),
    INDEX idx_updates_project_date (project_id, update_date DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Admin → Project assignments (which admins manage which projects).
-- ---------------------------------------------------------------------
CREATE TABLE admin_projects (
    admin_id      INT UNSIGNED NOT NULL,
    project_id    INT UNSIGNED NOT NULL,
    assigned_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (admin_id, project_id),
    CONSTRAINT fk_ap_admin FOREIGN KEY (admin_id)
        REFERENCES admins(id) ON DELETE CASCADE,
    CONSTRAINT fk_ap_project FOREIGN KEY (project_id)
        REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Notifications — shown live to admins and clients.
-- ---------------------------------------------------------------------
CREATE TABLE notifications (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipient_type ENUM('admin','client') NOT NULL,
    recipient_id   INT UNSIGNED NOT NULL,
    type           ENUM('status_update','milestone','comment','alert')
                   NOT NULL DEFAULT 'status_update',
    title          VARCHAR(255) NOT NULL,
    message        TEXT,
    reference_id   INT UNSIGNED DEFAULT NULL,
    is_read        TINYINT(1) NOT NULL DEFAULT 0,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notif_recipient (recipient_type, recipient_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- User sessions — server-side sessions stored in DB (secure + revocable).
-- ---------------------------------------------------------------------
CREATE TABLE user_sessions (
    id          VARCHAR(128) NOT NULL PRIMARY KEY,
    user_type   ENUM('admin','client') NOT NULL,
    user_id     INT UNSIGNED NOT NULL,
    ip_address  VARCHAR(45) DEFAULT NULL,
    user_agent  VARCHAR(512) DEFAULT NULL,
    expires_at  DATETIME NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sessions_user (user_type, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Audit log — record every admin write action.
-- ---------------------------------------------------------------------
CREATE TABLE audit_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_type  ENUM('admin','client','system') NOT NULL DEFAULT 'admin',
    actor_id    INT UNSIGNED DEFAULT NULL,
    action      VARCHAR(100) NOT NULL,
    entity      VARCHAR(50) DEFAULT NULL,
    entity_id   INT UNSIGNED DEFAULT NULL,
    details     JSON DEFAULT NULL,
    ip_address  VARCHAR(45) DEFAULT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_actor (actor_type, actor_id),
    INDEX idx_audit_entity (entity, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Login attempts — for rate limiting / lockout.
-- ---------------------------------------------------------------------
CREATE TABLE login_attempts (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identifier  VARCHAR(255) NOT NULL,
    ip_address  VARCHAR(45) NOT NULL,
    success     TINYINT(1) NOT NULL DEFAULT 0,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_attempts_id_ip (identifier, ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
