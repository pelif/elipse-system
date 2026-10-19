<?php

namespace Tests\Integration\UseCase\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\UseCase\User\DTO\UserInputDto;
use App\UseCase\User\CreateUserUseCase;
use App\UseCase\User\DeleteUserUseCase;
use App\UseCase\User\GetUserUseCase;
use App\UseCase\User\ListUserUseCase;
use App\UseCase\User\UpdateUserUseCase;
use Mockery;
use Tests\TestCase;

class UserUseCaseTest extends TestCase
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

    public function test_create_user_use_case(): void
    {
        $input = new UserInputDto(
            id: 'User001',
            name: 'User 001',
            email: 'user.001@myemail.com.br',
            password: '123456',
            role: 'comercial'
        );

        $userEntity = new User(
            id: $input->id,
            name: $input->name,
            email: new Email($input->email),
            password: $input->password,
            role: 'commercial'
        );

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->andReturn($userEntity);

        $useCase = new CreateUserUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertEquals('User001', $output->id);
        $this->assertEquals('User 001', $output->name);
        $this->assertEquals('user.001@myemail.com.br', $output->email);
        $this->assertNotNull($output->password);
        $this->assertEquals('Comercial', $output->role);
    }

    public function test_update_user_use_case(): void
    {
        $input = new UserInputDto(
            id: 'updated-user-uuid',
            name: 'updated user',
            email: 'updated.user@mymail.com',
            password: '123234345456',
            role: 'administrador'
        );

        $userEntity = new User(
            id: 'updated-user-uuid',
            name: $input->name,
            email: new Email($input->email),
            password: $input->password,
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('updated-user-uuid')
            ->andReturn($userEntity);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->andReturn($userEntity);

        $useCase = new UpdateUserUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertEquals('updated-user-uuid', $output->id);
        $this->assertEquals('updated user', $output->name);
        $this->assertEquals('updated.user@mymail.com', $output->email);
        $this->assertNotNull($output->password);
        $this->assertEquals('Administrador', $output->role);
    }

    public function test_get_user_use_case(): void
    {
        $input = new UserInputDto(
            id: 'get-user-uuid',
            name: 'get user',
            email: 'get.user@mymail.com',
            password: '123234345456',
            role: 'administrador'
        );

        $userEntity = new User(
            id: 'get-user-uuid',
            name: $input->name,
            email: new Email($input->email),
            password: $input->password,
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('get-user-uuid')
            ->andReturn($userEntity);

        $useCase = new GetUserUseCase($this->repository);
        $output = $useCase->execute($input->id);

        $this->assertEquals('get-user-uuid', $output->id);
        $this->assertEquals('get user', $output->name);
        $this->assertEquals('get.user@mymail.com', $output->email);
        $this->assertNotNull($output->password);
        $this->assertEquals('Administrador', $output->role);
    }

    public function test_list_user_use_case(): void
    {

        $userEntity = new User(
            id: 'list-user-uuid',
            name: 'list user',
            email: new Email('list.user@mymail.com'),
            password: '123234345456',
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findAll')
            ->once()
            ->with([])
            ->andReturn(['data' => [$userEntity], 'meta' => []]);

        $useCase = new ListUserUseCase($this->repository);
        $output = $useCase->execute([]);

        $this->assertIsArray($output['data']);
        $this->assertCount(1, $output['data']);
        $this->assertEquals('list-user-uuid', $output['data'][0]->id);
        $this->assertEquals('list user', $output['data'][0]->name);
        $this->assertEquals('list.user@mymail.com', $output['data'][0]->email);
        $this->assertNotNull($output['data'][0]->password);
        $this->assertEquals('Administrador', $output['data'][0]->role);
    }

    public function test_delete_user_use_case(): void
    {
        $input = new UserInputDto(
            id: 'delete-user-uuid',
            name: 'delete user',
            email: 'delete.user@mymail.com',
            password: '123234345456',
            role: 'administrador'
        );

        $userEntity = new User(
            id: $input->id,
            name: $input->name,
            email: new Email($input->email),
            password: $input->password,
            role: 'admin'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('delete-user-uuid')
            ->andReturn($userEntity);

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($userEntity)
            ->andReturn(true);

        $useCase = new DeleteUserUseCase($this->repository);
        $output = $useCase->execute($input->id);

        $this->assertTrue($output);
    }
}
