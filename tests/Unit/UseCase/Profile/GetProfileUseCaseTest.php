<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Profile;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\Profile\DTO\ProfileOutputDto;
use App\UseCase\Profile\GetProfileUseCase;
use Exception;
use Mockery;
use Tests\TestCase;

class GetProfileUseCaseTest extends TestCase
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

    public function test_throws_exception_when_user_not_found(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('999')
            ->andReturn(null);

        $useCase = new GetProfileUseCase($this->repository);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User not found');

        $useCase->execute('999');
    }

    public function test_returns_profile_output_dto_with_admin_role_formatted(): void
    {
        $user = new User(
            id: 'user-admin-uuid',
            name: 'Admin User',
            email: new Email('admin@example.com'),
            password: 'hashed_password',
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('user-admin-uuid')
            ->andReturn($user);

        $useCase = new GetProfileUseCase($this->repository);
        $output = $useCase->execute('user-admin-uuid');

        $this->assertInstanceOf(ProfileOutputDto::class, $output);
        $this->assertSame('user-admin-uuid', $output->id);
        $this->assertSame('Admin User', $output->name);
        $this->assertSame('admin@example.com', $output->email);
        $this->assertSame('admin', $output->role);
        $this->assertSame('Administrador', $output->roleFormatted);
    }

    public function test_returns_profile_output_dto_with_commercial_role_formatted(): void
    {
        $user = new User(
            id: 'user-commercial-uuid',
            name: 'Commercial User',
            email: new Email('commercial@example.com'),
            password: 'hashed_password',
            role: 'commercial'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('user-commercial-uuid')
            ->andReturn($user);

        $useCase = new GetProfileUseCase($this->repository);
        $output = $useCase->execute('user-commercial-uuid');

        $this->assertInstanceOf(ProfileOutputDto::class, $output);
        $this->assertSame('commercial', $output->role);
        $this->assertSame('Comercial', $output->roleFormatted);
    }

    public function test_returns_profile_output_dto_with_production_role_formatted(): void
    {
        $user = new User(
            id: 'user-production-uuid',
            name: 'Production User',
            email: new Email('production@example.com'),
            password: 'hashed_password',
            role: 'production'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('user-production-uuid')
            ->andReturn($user);

        $useCase = new GetProfileUseCase($this->repository);
        $output = $useCase->execute('user-production-uuid');

        $this->assertInstanceOf(ProfileOutputDto::class, $output);
        $this->assertSame('production', $output->role);
        $this->assertSame('Produção', $output->roleFormatted);
    }

    public function test_returns_profile_output_dto_with_finance_role_formatted(): void
    {
        $user = new User(
            id: 'user-finance-uuid',
            name: 'Finance User',
            email: new Email('finance@example.com'),
            password: 'hashed_password',
            role: 'finance'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('user-finance-uuid')
            ->andReturn($user);

        $useCase = new GetProfileUseCase($this->repository);
        $output = $useCase->execute('user-finance-uuid');

        $this->assertInstanceOf(ProfileOutputDto::class, $output);
        $this->assertSame('finance', $output->role);
        $this->assertSame('Financeiro', $output->roleFormatted);
    }

    public function test_output_dto_does_not_expose_password(): void
    {
        $user = new User(
            id: 'user-safe-uuid',
            name: 'Safe User',
            email: new Email('safe@example.com'),
            password: 'secret_password_hash',
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($user);

        $useCase = new GetProfileUseCase($this->repository);
        $output = $useCase->execute('user-safe-uuid');

        $this->assertInstanceOf(ProfileOutputDto::class, $output);
        $this->assertFalse(property_exists($output, 'password'));
    }
}
