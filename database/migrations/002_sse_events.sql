-- =====================================================================
-- SERAPH BUILD CONSTRUCTION — SSE pub/sub events table
-- Lightweight channel-based event queue used by the SSE endpoints.
-- Works on shared hosting (no Redis required): writers insert rows,
-- SSE readers poll for new rows after their last seen ID.
-- =====================================================================

USE seraph_construction;

CREATE TABLE IF NOT EXISTS sse_events (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    channel     VARCHAR(100) NOT NULL,
    event       VARCHAR(50) NOT NULL DEFAULT 'message',
    payload     JSON NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_channel_id (channel, id),
    INDEX idx_id (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cleanup job (run via cron hourly): delete events older than 1 day
-- DELETE FROM sse_events WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY);
