<?php

namespace Tests\Integration\UseCase\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\User\NewPasswordUseCase;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewPasswordUseCaseTest extends TestCase
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
        $id = 999;

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        $useCase = new NewPasswordUseCase($this->repository);
        $output = $useCase->execute($id);

        $this->assertFalse($output->success);
        $this->assertEquals('Usuário não encontrado', $output->error);
    }

    public function test_it_creates_token_and_sends_notification(): void
    {
        $id = 1;

        $userEntity = new User(
            id: '1',
            name: 'Test',
            email: new Email('test@test.com'),
            password: 'pass',
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($userEntity);

        // Rely on db connection
        \App\Models\User::create([
            'id' => 1,
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'pas',
            'role' => 'admin'
        ]);

        // Mock Password Broker
        \Illuminate\Support\Facades\Password::shouldReceive('broker')
            ->once()
            ->andReturnSelf();
        \Illuminate\Support\Facades\Password::shouldReceive('createToken')
            ->once()
            ->andReturn('fake-token');

        $useCase = new NewPasswordUseCase($this->repository);
        $output = $useCase->execute($id);

        $this->assertTrue($output->success);
    }
}
