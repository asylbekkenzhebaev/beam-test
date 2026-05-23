<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use Throwable;

class EntityBroadcastService
{
    public const CHANNEL = 'dashboard.entities';

    public const EVENT = 'entity.changed';

    public function broadcast(
        string $entity,
        string $action,
        int $id,
        string $title,
        string $message,
        ?string $url = null,
    ): ?array {
        if (! $this->isConfigured()) {
            return null;
        }

        $payload = $this->buildPayload($entity, $action, $id, $title, $message, $url);

        $this->sendPayload($payload);

        return $payload;
    }

    public function broadcastAfterResponse(
        string $entity,
        string $action,
        int $id,
        string $title,
        string $message,
        ?string $url = null,
    ): ?array {
        if (! $this->isConfigured()) {
            return null;
        }

        $payload = $this->buildPayload($entity, $action, $id, $title, $message, $url);

        defer(function () use ($payload): void {
            $this->sendPayload($payload);
        }, name: sprintf('dashboard-broadcast-%s-%s-%d', $entity, $action, $id));

        return $payload;
    }

    public function buildPayload(
        string $entity,
        string $action,
        int $id,
        string $title,
        string $message,
        ?string $url = null,
    ): array {
        return [
            'entity' => $entity,
            'action' => $action,
            'id' => $id,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function isConfigured(): bool
    {
        $config = config('services.pusher', []);

        return filled($config['app_id'] ?? null)
            && filled($config['key'] ?? null)
            && filled($config['secret'] ?? null)
            && filled($config['cluster'] ?? null);
    }

    private function makeClient(): Pusher
    {
        $config = config('services.pusher', []);

        return new Pusher(
            $config['key'],
            $config['secret'],
            $config['app_id'],
            [
                'cluster' => $config['cluster'],
                'useTLS' => (bool) ($config['use_tls'] ?? true),
                'timeout' => (float) ($config['timeout'] ?? 1),
            ],
        );
    }

    private function sendPayload(array $payload): void
    {
        try {
            $this->makeClient()->trigger(self::CHANNEL, self::EVENT, $payload);
        } catch (Throwable $exception) {
            Log::error('Pusher broadcast failed.', [
                'channel' => self::CHANNEL,
                'event' => self::EVENT,
                'payload' => $payload,
                'message' => $exception->getMessage(),
            ]);

            report($exception);
        }
    }
}
