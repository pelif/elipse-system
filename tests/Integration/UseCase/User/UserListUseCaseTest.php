<?php

namespace Tests\Integration\UseCase\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\UseCase\User\DTO\UserOutputDto;
use App\UseCase\User\ListUserUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserListUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepositoryInterface::class);
    }

    public function test_list_all_users(): void
    {

        $useCase = new ListUserUseCase($this->repository);
        $users = $useCase->execute([]);

        $this->assertIsArray($users['data']);
        $this->assertContainsOnlyInstancesOf(UserOutputDto::class, $users['data']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
