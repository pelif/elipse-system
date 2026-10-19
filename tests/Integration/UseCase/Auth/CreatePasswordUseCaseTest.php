<?php

namespace Tests\Integration\UseCase\Auth;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\UseCase\User\CreatePasswordUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePasswordUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = \Mockery::mock(UserRepositoryInterface::class);
    }

    public function test_it_creates_password_with_valid_data(): void
    {
        $input = [
            'email' => 'user@test.com',
            'token' => 'fake-token'
        ];

        \App\Models\User::create([
            'name' => 'User',
            'email' => $input['email'],
            'password' => 'pass',
            'role' => 'admin',
        ]);

        $userEntity = new \App\Domain\User\Entities\User(
            id: 'u1',
            name: 'User',
            email: new \App\Domain\User\ValueObjects\Email($input['email']),
            password: 'pass',
            role: 'administrador'
        );

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($input['email'])
            ->andReturn($userEntity);

        // Mock facades to avoid database calls
        \Illuminate\Support\Facades\Password::shouldReceive('broker')
            ->once()
            ->andReturnSelf();
        \Illuminate\Support\Facades\Password::shouldReceive('tokenExists')
            ->andReturn(true);

        $useCase = new CreatePasswordUseCase($this->repository);

        $output = $useCase->execute($input);

        $this->assertTrue($output->success);
    }

    public function test_it_throws_exception_when_user_is_not_found(): void
    {
        $input = [
            'email' => 'user@test.com',
            'token' => 'fake-token'
        ];

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($input['email'])
            ->andReturn(null);

        $useCase = new CreatePasswordUseCase($this->repository);

        $output = $useCase->execute($input);

        $this->assertFalse($output->success);
        $this->assertEquals('Usuário não encontrado', $output->error);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
