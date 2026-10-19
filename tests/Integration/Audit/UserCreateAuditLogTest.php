<?php

namespace Tests\Integration\Audit;

use App\Jobs\WriteAuditLogJob;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Usa DB sqlite :memory: e QUEUE_CONNECTION=sync (phpunit.xml): não toca no MySQL de desenvolvimento.
 *
 * Para ver o dump no terminal: php artisan test tests/Integration/Audit/UserCreateAuditLogTest.php 2>&1
 * O JSON também é salvo em sys_get_temp_dir()/elipse_audit_user_create_dump.json
 */
class UserCreateAuditLogTest extends TestCase
{
    /*
     * DatabaseMigrations (sem transação de RefreshDatabase): jobs com afterCommit() executam no sync queue.
     * RefreshDatabase mantém uma transação aberta; o commit só no fim do teste e o job de auditoria não roda a tempo.
     */
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        // UserObserver envia e-mail de reset; .env do Docker pode usar SMTP (não depende disso neste teste).
        Mail::fake();
    }

    public function test_create_user_dispatches_write_audit_log_job(): void
    {
        Bus::fake([WriteAuditLogJob::class]);

        User::factory()->create([
            'name' => 'Bus Fake User',
            'email' => 'bus.fake.audit@example.invalid',
        ]);

        Bus::assertDispatched(WriteAuditLogJob::class, function (WriteAuditLogJob $job): bool {
            return ($job->payload['action'] ?? null) === 'created'
                && ($job->payload['resource']['model'] ?? null) === User::class;
        });
    }

    public function test_factory_create_user_produces_audit_payload_matching_log_context(): void
    {
        $auditPayloads = [];

        Event::listen(MessageLogged::class, function (MessageLogged $e) use (&$auditPayloads): void {
            if ($e->message === 'audit') {
                $auditPayloads[] = $e->context;
            }
        });

        $user = User::factory()->create([
            'name' => 'Audit Test User',
            'email' => 'audit.test.user@example.invalid',
        ]);

        $this->assertNotEmpty(
            $auditPayloads,
            'Esperado registro de auditoria após create (fila sync + WriteAuditLogJob).'
        );

        $payload = $auditPayloads[0];
        $this->assertSame('created', $payload['action']);
        $this->assertSame(User::class, $payload['resource']['model']);
        $this->assertSame((string) $user->getKey(), (string) $payload['resource']['key']);

        $attributes = $payload['changes']['attributes'] ?? [];
        $this->assertArrayHasKey('password', $attributes);
        $this->assertSame('[REDACTED]', $attributes['password']);
        if (array_key_exists('remember_token', $attributes)) {
            $this->assertSame('[REDACTED]', $attributes['remember_token']);
        }

        $dump = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $dumpFile = sys_get_temp_dir() . '/elipse_audit_user_create_dump.json';
        file_put_contents($dumpFile, $dump . "\n");

        fwrite(STDERR, "\n[audit-test] Dump do contexto de log (canal elipse_audit, mensagem audit):\n" . $dump . "\n");
        fwrite(STDERR, '[audit-test] Arquivo: ' . $dumpFile . "\n\n");
    }
}
