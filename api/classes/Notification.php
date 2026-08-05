<?php
/**
 * SERAPH BUILD CONSTRUCTION — Notification model.
 */

declare(strict_types=1);

class Notification
{
    public static function create(string $recipientType, int $recipientId, string $type, string $title, ?string $message = null, ?int $referenceId = null): int
    {
        return Database::insert(
            "INSERT INTO notifications (recipient_type, recipient_id, type, title, message, reference_id)
             VALUES (:rt, :rid, :type, :title, :message, :ref)",
            [
                ':rt'      => $recipientType,
                ':rid'     => $recipientId,
                ':type'    => $type,
                ':title'   => $title,
                ':message' => $message,
                ':ref'     => $referenceId,
            ]
        );
    }

    public static function forUser(string $recipientType, int $recipientId, bool $unreadOnly = false): array
    {
        $sql = "SELECT * FROM notifications
                 WHERE recipient_type = :rt AND recipient_id = :rid";
        if ($unreadOnly) {
            $sql .= ' AND is_read = 0';
        }
        $sql .= ' ORDER BY created_at DESC LIMIT 50';

        return Database::all($sql, [':rt' => $recipientType, ':rid' => $recipientId]);
    }

    public static function unreadCount(string $recipientType, int $recipientId): int
    {
        return (int)Database::scalar(
            'SELECT COUNT(*) FROM notifications
              WHERE recipient_type = :rt AND recipient_id = :rid AND is_read = 0',
            [':rt' => $recipientType, ':rid' => $recipientId]
        );
    }

    public static function markRead(string $recipientType, int $recipientId, ?int $notificationId = null): void
    {
        $sql = 'UPDATE notifications SET is_read = 1 WHERE recipient_type = :rt AND recipient_id = :rid';
        $params = [':rt' => $recipientType, ':rid' => $recipientId];
        if ($notificationId !== null) {
            $sql .= ' AND id = :nid';
            $params[':nid'] = $notificationId;
        }
        Database::execute($sql, $params);
    }

    /** Notify all admins assigned to a project, plus the client. */
    public static function notifyProjectParties(int $projectId, int $clientId, string $type, string $title, ?string $message = null, ?int $referenceId = null): void
    {
        $admins = Project::assignedAdmins($projectId);
        foreach ($admins as $a) {
            self::create('admin', (int)$a['id'], $type, $title, $message, $referenceId);
        }
        self::create('client', $clientId, $type, $title, $message, $referenceId);
    }
}
