<?php

namespace Tests\Integration\UseCase\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\User\StoreNewPasswordUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class StoreNewPasswordUseCaseTest extends TestCase
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

    public function test_it_returns_error_on_failure(): void
    {
        $input = ['id' => 1, 'password' => 'new-pass'];

        $this->repository
            ->shouldReceive('updatePassword')
            ->once()
            ->with($input['id'], $input['password'])
            ->andThrow(new \Exception('Update Failed'));

        $useCase = new StoreNewPasswordUseCase($this->repository);
        
        $this->expectException(\Exception::class);
        $output = $useCase->execute($input);
    }

    public function test_it_returns_success_on_update(): void
    {
        $input = ['id' => 1, 'password' => 'new-pass'];

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
            ->with($input['id'], $input['password'])
            ->andReturn($userEntity);

        $useCase = new StoreNewPasswordUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertTrue($output->success);
        $this->assertNull($output->error);
    }
}
