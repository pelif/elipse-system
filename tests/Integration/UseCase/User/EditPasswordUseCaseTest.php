<?php

namespace Tests\Integration\UseCase\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\User\EditPasswordUseCase;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditPasswordUseCaseTest extends TestCase
{
    use RefreshDatabase;
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(UserRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_error_if_user_not_found(): void
    {
        $input = ['email' => 'notfound@test.com', 'token' => 'asdf'];

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($input['email'])
            ->andReturn(null);

        // Eloquent queries will hit SQLite

        $useCase = new EditPasswordUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertFalse($output->success);
        $this->assertEquals('Usuário não encontrado', $output->error);
    }

    public function test_it_edits_password_with_valid_token(): void
    {
        $input = [
            'email' => 'valid@test.com',
            'token' => 'valid-token'
        ];

        $userEntity = new User(
            id: 'u1',
            name: 'User',
            email: new Email($input['email']),
            password: 'pass',
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($input['email'])
            ->andReturn($userEntity);

        // Mock Eloquent query hits SQLite natively
        \App\Models\User::create([
            'id' => 1,
            'name' => 'Test',
            'email' => 'valid@test.com',
            'password' => 'pas',
            'role' => 'admin'
        ]);

        // Mock Password Broker
        \Illuminate\Support\Facades\Password::shouldReceive('broker')
            ->once()
            ->andReturnSelf();
        \Illuminate\Support\Facades\Password::shouldReceive('tokenExists')
            ->once()
            ->andReturn(true);

        $useCase = new EditPasswordUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertTrue($output->success);
        $this->assertEquals('valid@test.com', $output->user->email);
        $this->assertTrue($output->hasToken);
    }
}
