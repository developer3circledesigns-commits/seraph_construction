<?php
/**
 * SERAPH BUILD CONSTRUCTION — Daily update model.
 */

declare(strict_types=1);

class DailyUpdate
{
    public const STATUSES = ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'];

    public static function find(int $id): ?array
    {
        return Database::one(
            "SELECT u.*, p.name AS project_name, p.client_id, a.full_name AS admin_name
               FROM daily_updates u
               JOIN projects p ON p.id = u.project_id
               JOIN admins a ON a.id = u.admin_id
              WHERE u.id = :id",
            [':id' => $id]
        );
    }

    /** All updates for a project, newest first. */
    public static function forProject(int $projectId, ?int $limit = null): array
    {
        $sql = "SELECT u.*, a.full_name AS admin_name
                  FROM daily_updates u
                  JOIN admins a ON a.id = u.admin_id
                 WHERE u.project_id = :pid
                 ORDER BY u.update_date DESC, u.id DESC";
        $params = [':pid' => $projectId];

        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$limit;
        }

        return Database::all($sql, $params);
    }

    /** Check if a date already has an update for the project. */
    public static function existsForDate(int $projectId, string $date): bool
    {
        return (bool)Database::scalar(
            'SELECT 1 FROM daily_updates WHERE project_id = :pid AND update_date = :d',
            [':pid' => $projectId, ':d' => $date]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO daily_updates
                (project_id, admin_id, update_date, status, progress_percentage,
                 title, description, images, materials_used, labor_count,
                 next_day_plan, is_milestone)
             VALUES
                (:project_id, :admin_id, :update_date, :status, :progress_percentage,
                 :title, :description, :images, :materials_used, :labor_count,
                 :next_day_plan, :is_milestone)",
            [
                ':project_id'          => $data['project_id'],
                ':admin_id'            => $data['admin_id'],
                ':update_date'         => $data['update_date'],
                ':status'              => $data['status'],
                ':progress_percentage' => $data['progress_percentage'],
                ':title'               => $data['title'],
                ':description'         => $data['description'] ?? null,
                ':images'              => json_encode($data['images'] ?? []),
                ':materials_used'      => $data['materials_used'] ?? null,
                ':labor_count'         => $data['labor_count'] !== '' ? $data['labor_count'] : null,
                ':next_day_plan'       => $data['next_day_plan'] ?? null,
                ':is_milestone'        => $data['is_milestone'] ? 1 : 0,
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        Database::execute(
            "UPDATE daily_updates SET
                update_date         = :update_date,
                status              = :status,
                progress_percentage = :progress_percentage,
                title               = :title,
                description         = :description,
                images              = :images,
                materials_used      = :materials_used,
                labor_count         = :labor_count,
                next_day_plan       = :next_day_plan,
                is_milestone        = :is_milestone
              WHERE id = :id",
            [
                ':update_date'         => $data['update_date'],
                ':status'              => $data['status'],
                ':progress_percentage' => $data['progress_percentage'],
                ':title'               => $data['title'],
                ':description'         => $data['description'] ?? null,
                ':images'              => json_encode($data['images'] ?? []),
                ':materials_used'      => $data['materials_used'] ?? null,
                ':labor_count'         => $data['labor_count'] !== '' ? $data['labor_count'] : null,
                ':next_day_plan'       => $data['next_day_plan'] ?? null,
                ':is_milestone'        => $data['is_milestone'] ? 1 : 0,
                ':id'                  => $id,
            ]
        );
    }

    /** Replace the stored image-id list for an update. */
    public static function setImages(int $id, array $imageIds): void
    {
        Database::execute(
            'UPDATE daily_updates SET images = :images WHERE id = :id',
            [':images' => json_encode($imageIds), ':id' => $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM daily_updates WHERE id = :id', [':id' => $id]);
    }

    public static function validate(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Title is required.';
        }
        if (empty($data['update_date']) || !strtotime($data['update_date'])) {
            $errors[] = 'A valid update date is required.';
        }
        if (!in_list($data['status'] ?? null, self::STATUSES)) {
            $errors[] = 'Invalid status.';
        }
        if (($data['progress_percentage'] ?? '') !== '' && (!is_numeric($data['progress_percentage']) || (int)$data['progress_percentage'] < 0 || (int)$data['progress_percentage'] > 100)) {
            $errors[] = 'Progress must be between 0 and 100.';
        }
        if (($data['labor_count'] ?? '') !== '' && (!is_numeric($data['labor_count']) || (int)$data['labor_count'] < 0)) {
            $errors[] = 'Labor count must be a valid number.';
        }

        return $errors;
    }
}
