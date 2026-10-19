<?php

namespace Tests\Integration\UseCase\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Services\PasswordHasher;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\User\LoginUserUseCase;
use Mockery;
use Tests\TestCase;

class LoginUserUseCaseTest extends TestCase
{
    private $repository;
    private $hasher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(UserRepositoryInterface::class);
        $this->hasher = Mockery::mock(PasswordHasher::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_error_if_user_not_found(): void
    {
        $input = [
            'email' => 'invalid@test.com',
            'password' => 'secret'
        ];

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($input['email'])
            ->andReturn(null);

        $useCase = new LoginUserUseCase($this->repository, $this->hasher);
        $output = $useCase->execute($input);

        $this->assertFalse($output->success);
        $this->assertEquals('Usuário não encontrado', $output->error);
    }

    public function test_it_returns_error_if_password_invalid(): void
    {
        $input = [
            'email' => 'valid@test.com',
            'password' => 'wrong-pass'
        ];

        $userEntity = new User(
            id: 'uuid-1',
            name: 'Test',
            email: new Email($input['email']),
            password: 'hashed-password',
            role: 'production'
        );

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($input['email'])
            ->andReturn($userEntity);

        $this->hasher
            ->shouldReceive('verify')
            ->once()
            ->with('wrong-pass', 'hashed-password')
            ->andReturn(false);

        $useCase = new LoginUserUseCase($this->repository, $this->hasher);
        $output = $useCase->execute($input);

        $this->assertFalse($output->success);
        $this->assertEquals('Credenciais inválidas', $output->error);
    }

    public function test_it_returns_success_if_credentials_valid(): void
    {
        $input = [
            'email' => 'valid@test.com',
            'password' => 'correct-pass'
        ];

        $userEntity = new User(
            id: 'uuid-1',
            name: 'Test',
            email: new Email($input['email']),
            password: 'hashed-password',
            role: 'production'
        );

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($input['email'])
            ->andReturn($userEntity);

        $this->hasher
            ->shouldReceive('verify')
            ->once()
            ->with('correct-pass', 'hashed-password')
            ->andReturn(true);

        $useCase = new LoginUserUseCase($this->repository, $this->hasher);
        $output = $useCase->execute($input);

        $this->assertTrue($output->success);
        $this->assertEquals($userEntity, $output->user);
    }
}
