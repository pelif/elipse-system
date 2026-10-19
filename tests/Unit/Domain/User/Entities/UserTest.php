<?php

namespace Tests\Unit\Domain\User\Entities;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;

use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_creates_user_with_valid_data(): void
    {
        $user = new User(
            't01',
            'John Doe',
            new Email('john.doe@example.com'),
            'password',
            'user'
        );

        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john.doe@example.com', $user->getEmail()->getValue());
        $this->assertEquals('password', $user->getPassword());
        $this->assertEquals('user', $user->getRole());
    }

    public function test_it_throws_exception_when_email_is_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new User(
            'c01',
            'John Doe',
            new Email('invalid-email'),
            'password',
            'user'
        );
    }
}
