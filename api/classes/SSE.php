<?php
/**
 * SERAPH BUILD CONSTRUCTION — Server-Sent Events service.
 *
 * Shared-hosting friendly pub/sub:
 *  - Writers (admin posts an update) call SSE::broadcast() → inserts into sse_events.
 *  - Readers open an EventSource; the endpoint streams and polls for new rows
 *    after the client's `Last-Event-ID`, sleeping ~1.5s between checks.
 *  - Heartbeat comment is emitted every 30s to keep proxies from dropping.
 *
 * This requires no daemon, no Redis, no WebSocket server — pure PHP-FPM.
 */

declare(strict_types=1);

class SSE
{
    public const TTL_SECONDS = 120;   // max stream lifetime per connection
    public const SLEEP_SECONDS = 2;   // poll interval

    /**
     * Broadcast an event to a channel. Fire-and-forget insert.
     */
    public static function broadcast(string $channel, string $event, array $payload): void
    {
        try {
            Database::insert(
                'INSERT INTO sse_events (channel, event, payload) VALUES (:c, :e, :p)',
                [
                    ':c' => $channel,
                    ':e' => $event,
                    ':p' => json_encode($payload),
                ]
            );
        } catch (Throwable $e) {
            // Non-fatal: real-time is best-effort; DB writes already persisted.
            error_log('SSE broadcast failed: ' . $e->getMessage());
        }
    }

    /** Standard channel name for a project. */
    public static function projectChannel(int $projectId): string
    {
        return 'project_' . $projectId;
    }

    /** Standard channel name for a user (admin or client). */
    public static function userChannel(string $userType, int $userId): string
    {
        return $userType . '_' . $userId;
    }

    /**
     * Stream events for the given channels.
     * Blocks until time limit, client disconnect, or no channels provided.
     *
     * @param array  $channels   e.g. ['project_1', 'client_2']
     * @param string $lastEventId client-sent Last-Event-ID (resume point)
     */
    public static function stream(array $channels, string $lastEventId = '0'): void
    {
        if (!$channels) {
            return;
        }

        // Release the session lock before streaming. PHP holds a write lock on
        // the session file for the whole request; without closing it, every
        // other request from the same browser (page loads, APIs) would block
        // until this long-lived stream ends.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache, no-transform');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        // Avoid client/proxy buffering on slow connections.
        if (ob_get_level()) {
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
        }
        @set_time_limit(self::TTL_SECONDS);

        echo ": connected\n\n";
        flush();

        $resuming = $lastEventId !== '' && $lastEventId !== '0';
        if ($resuming) {
            $lastId = max(0, (int)$lastEventId);
        } else {
            // Fresh connection: do NOT replay the event history. Starting from
            // the current max id means only events created AFTER this connection
            // is established are streamed. Replaying history from id 0 caused the
            // clients' auto-refresh handler to reload the page repeatedly in a
            // loop. Re-connections resume via the Last-Event-ID header instead.
            try {
                $lastId = (int)Database::scalar('SELECT COALESCE(MAX(id), 0) FROM sse_events');
            } catch (Throwable $e) {
                $lastId = 0;
            }
        }
        $start  = time();
        $lastHeartbeat = $start;

        while (true) {
            // Client disconnected?
            if (connection_aborted()) {
                break;
            }

            $rows = [];
            try {
                $params = array_merge([$lastId], $channels);
                $inPlaceholders = implode(',', array_fill(0, count($channels), '?'));
                $rows = Database::all(
                    "SELECT id, channel, event, payload FROM sse_events
                      WHERE id > ? AND channel IN ({$inPlaceholders})
                      ORDER BY id ASC
                      LIMIT 100",
                    $params
                );
            } catch (Throwable $e) {
                // Transient DB hiccup (e.g. connection dropped). Keep the stream
                // alive instead of dying and triggering a client reconnect storm.
                error_log('SSE poll failed: ' . $e->getMessage());
            }

            foreach ($rows as $row) {
                echo 'id: ' . $row['id'] . "\n";
                echo 'event: ' . $row['event'] . "\n";
                echo 'data: ' . $row['payload'] . "\n\n";
                $lastId = (int)$row['id'];
            }
            flush();

            // Heartbeat keeps the connection alive through proxies.
            $now = time();
            if ($now - $lastHeartbeat >= 30) {
                echo ': hb ' . $now . "\n\n";
                flush();
                $lastHeartbeat = $now;
            }

            if ($now - $start >= self::TTL_SECONDS) {
                echo ": end\n\n";
                flush();
                break;
            }

            sleep(self::SLEEP_SECONDS);
        }
    }

    /** Send one named event immediately (e.g. connection confirmation). */
    public static function send(string $event, array $data, int $id = 0): void
    {
        echo 'id: ' . $id . "\n";
        echo 'event: ' . $event . "\n";
        echo 'data: ' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n";
        flush();
    }

    private static function placeholders(array $channels): string
    {
        return implode(',', array_fill(0, count($channels), '?'));
    }}
