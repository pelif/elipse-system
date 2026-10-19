<?php

namespace Tests\Integration\UseCase\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\User\GetUserByEmailUseCase;
use Mockery;
use Tests\TestCase;

class GetUserByEmailUseCaseTest extends TestCase
{
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

    public function test_it_returns_user_output_dto_by_email(): void
    {
        $email = 'test@example.com';

        $userEntity = new User(
            id: 'uuid-1',
            name: 'Test',
            email: new Email($email),
            password: 'pass',
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($userEntity);

        $useCase = new GetUserByEmailUseCase($this->repository);
        $output = $useCase->execute($email);

        $this->assertEquals('uuid-1', $output->id);
        $this->assertEquals('Test', $output->name);
        $this->assertEquals($email, $output->email);
        $this->assertEquals('Administrador', $output->role);
    }

    public function test_it_throws_exception_when_user_not_found(): void
    {
        $email = 'notfound@example.com';

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn(null);

        $useCase = new GetUserByEmailUseCase($this->repository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');
        $useCase->execute($email);
    }
}
