<?php

namespace Tests\Unit\Domain\User\ValueObjects;

use App\Domain\User\ValueObjects\Email;
use Tests\TestCase;

class EmailTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_with_valid_email_address(): void
    {
        $email = new Email('john.doe@example.com');
        $this->assertEquals('john.doe@example.com', $email->getValue());
    }

    public function test_it_throws_exception_when_email_is_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('invalid-email');
    }
}
