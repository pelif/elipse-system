<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WriteAuditLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(public array $payload)
    {
    }

    public function handle(): void
    {
        $channel = (string) (config('audit.channel', 'elipse_audit') ?? 'elipse_audit');
        $maxBytes = (int) (config('audit.max_payload_json_bytes', 65536) ?? 65536);

        $payload = $this->payload;
        if ($maxBytes > 0) {
            $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            if ($encoded !== false && strlen($encoded) > $maxBytes) {
                $payload = [
                    'occurred_at' => $payload['occurred_at'] ?? null,
                    'action' => $payload['action'] ?? null,
                    'actor' => isset($payload['actor']['id']) ? ['id' => $payload['actor']['id']] : null,
                    'resource' => $payload['resource'] ?? null,
                    '_audit_truncated' => true,
                    '_audit_truncated_reason' => 'max_payload_json_bytes',
                ];
            }
        }

        Log::channel($channel)->info('audit', $payload);
    }
}

