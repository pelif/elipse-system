<?php

namespace Tests\Integration\UseCase\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\User\DTO\PasswordInputDto;
use App\UseCase\User\UpdatePasswordUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UpdatePasswordUseCaseTest extends TestCase
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

    public function test_it_returns_error_on_exception(): void
    {
        $input = new PasswordInputDto(id: 1, password: 'new-pass');

        $this->repository
            ->shouldReceive('updatePassword')
            ->once()
            ->with((int)$input->id, $input->password)
            ->andThrow(new \Exception('DB Error'));

        $useCase = new UpdatePasswordUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertFalse($output->success);
        $this->assertEquals('Não foi possível criar a senha', $output->error);
    }

    public function test_it_returns_success_on_update(): void
    {
        $input = new PasswordInputDto(id: 1, password: 'new-pass');

        \App\Models\User::create([
            'id' => 1,
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'old-pass',
            'role' => 'admin',
        ]);

        $broker = Mockery::mock(\Illuminate\Auth\Passwords\PasswordBroker::class);
        $broker->shouldReceive('deleteToken')->once();
        \Illuminate\Support\Facades\Password::shouldReceive('broker')->once()->andReturn($broker);

        $userEntity = new User(
            id: '1',
            name: 'Test',
            email: new Email('test@test.com'),
            password: 'new-pass',
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('updatePassword')
            ->once()
            ->with((int)$input->id, $input->password)
            ->andReturn($userEntity);

        $useCase = new UpdatePasswordUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertTrue($output->success);
    }
}
