<?php
/**
 * SERAPH BUILD CONSTRUCTION — Audit log.
 */

declare(strict_types=1);

class Audit
{
    public static function log(
        string $actorType,
        ?int $actorId,
        string $action,
        ?string $entity = null,
        ?int $entityId = null,
        ?array $details = null
    ): void {
        Database::execute(
            "INSERT INTO audit_log (actor_type, actor_id, action, entity, entity_id, details, ip_address)
             VALUES (:at, :aid, :action, :entity, :eid, :details, :ip)",
            [
                ':at'      => $actorType,
                ':aid'     => $actorId,
                ':action'  => $action,
                ':entity'  => $entity,
                ':eid'     => $entityId,
                ':details' => $details ? json_encode($details) : null,
                ':ip'      => client_ip(),
            ]
        );
    }

    /** Shortcut for admin actions. */
    public static function admin(int $adminId, string $action, ?string $entity = null, ?int $entityId = null, ?array $details = null): void
    {
        self::log('admin', $adminId, $action, $entity, $entityId, $details);
    }

    public static function recent(int $limit = 50): array
    {
        return Database::all(
            'SELECT * FROM audit_log ORDER BY created_at DESC LIMIT ' . (int)$limit
        );
    }
}
