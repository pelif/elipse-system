<?php

namespace Tests\Integration\Audit;

use App\Jobs\WriteAuditLogJob;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthLoginLogoutAuditLogTest extends TestCase
{
    /*
     * DatabaseMigrations (sem transação de RefreshDatabase): jobs com afterCommit() executam no sync queue.
     * RefreshDatabase mantém uma transação aberta; o commit só no fim do teste e o job de auditoria não roda a tempo.
     */
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'localhost',
            ], 200),
        ]);

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_login_dispatches_write_audit_log_job(): void
    {
        Bus::fake([WriteAuditLogJob::class]);

        $user = User::factory()->create([
            'email' => 'auth.audit.login@example.invalid',
            // O cast `password => hashed` já aplica o hash; não usar bcrypt() aqui (evita double-hash).
            'password' => 'password-123',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password-123',
            'g-recaptcha-response' => 'test-token',
        ]);

        $response->assertRedirect(route('welcome'));

        Bus::assertDispatched(WriteAuditLogJob::class, function (WriteAuditLogJob $job) use ($user): bool {
            return ($job->payload['action'] ?? null) === 'login'
                && (string) ($job->payload['actor']['id'] ?? '') === (string) $user->getAuthIdentifier()
                && ($job->payload['request']['path'] ?? null) === 'login';
        });
    }

    public function test_logout_dispatches_write_audit_log_job(): void
    {
        Bus::fake([WriteAuditLogJob::class]);

        $user = User::factory()->create([
            'email' => 'auth.audit.logout@example.invalid',
            // O cast `password => hashed` já aplica o hash; não usar bcrypt() aqui (evita double-hash).
            'password' => 'password-123',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password-123',
            'g-recaptcha-response' => 'test-token',
        ])->assertRedirect(route('welcome'));

        $this->post('/logout')->assertRedirect(route('login'));

        Bus::assertDispatched(WriteAuditLogJob::class, function (WriteAuditLogJob $job) use ($user): bool {
            return ($job->payload['action'] ?? null) === 'logout'
                && (string) ($job->payload['actor']['id'] ?? '') === (string) $user->getAuthIdentifier()
                && ($job->payload['request']['path'] ?? null) === 'logout';
        });
    }
}
