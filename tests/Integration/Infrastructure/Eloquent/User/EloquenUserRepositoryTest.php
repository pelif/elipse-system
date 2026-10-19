<?php

namespace Tests\Integration\Infrastructure\Eloquent\User;

use App\Domain\User\Entities\User as UserEntity;
use App\Domain\User\ValueObjects\Email;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EloquenUserRepositoryTest extends TestCase
{

    use RefreshDatabase;

    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository();
    }

    public function test_create_user(): void
    {
        $userEntity = new UserEntity(
            id: 't01',
            name: 'User Test',
            email: new Email('user.test@mymailer.com'),
            password: '123131312.qweqweqweqwe',
            role: 'comercial'
        );

        $createdUser = $this->repository->create($userEntity);

        $this->assertNotNull($createdUser->getId());
        $this->assertEquals('User Test', $createdUser->getName());
        $this->assertEquals('user.test@mymailer.com', $createdUser->getEmail()->getValue());
        $this->assertNotNull($createdUser->getPassword());
        $this->assertEquals('commercial', $createdUser->getRole());
    }
}
