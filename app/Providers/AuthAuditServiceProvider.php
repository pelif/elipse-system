<?php

namespace App\Providers;

use App\Support\Audit\AuditPayloadFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AuthAuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            $payload = AuditPayloadFactory::fromAuthEvent('login', $event->user, [
                'guard' => $event->guard,
                'remember' => (bool) $event->remember,
            ]);

            Event::dispatch('audit.payload', [$payload]);
        });

        Event::listen(Logout::class, function (Logout $event): void {
            $payload = AuditPayloadFactory::fromAuthEvent('logout', $event->user, [
                'guard' => $event->guard,
            ]);

            Event::dispatch('audit.payload', [$payload]);
        });
    }
}

