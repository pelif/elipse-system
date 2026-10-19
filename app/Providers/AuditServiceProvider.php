<?php

namespace App\Providers;

use App\Jobs\WriteAuditLogJob;
use App\Support\Audit\AuditPayloadFactory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerEloquentAuditListener('created');
        $this->registerEloquentAuditListener('updated');
        $this->registerEloquentAuditListener('deleted');

        Event::listen('audit.payload', function (array $payload): void {
            $queue = (string) (config('audit.queue', 'audit') ?? 'audit');
            WriteAuditLogJob::dispatch($payload)
                ->onQueue($queue)
                ->afterCommit();
        });
    }

    private function registerEloquentAuditListener(string $action): void
    {
        Event::listen("eloquent.{$action}: *", function (string $eventName, array $data) use ($action): void {
            $model = $data[0] ?? null;
            if (!is_object($model)) {
                return;
            }

            $payload = AuditPayloadFactory::fromEloquentModelEvent($action, $model);
            if ($payload === null) {
                return;
            }

            // Hook point: other parts of the app can listen to 'audit.payload'
            // and handle persistence (e.g. queue job + log channel).
            Event::dispatch('audit.payload', [$payload]);
        });
    }
}

