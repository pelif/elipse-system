<?php

namespace Tests\Unit\Domain\EmailConfig\Entities;

use App\Domain\EmailConfig\Entities\EmailConfig;
use Tests\TestCase;

class EmailConfigTest extends TestCase
{
    public function test_it_stores_fields(): void
    {
        $entity = new EmailConfig(
            id: 'uuid-1',
            email: 'x@example.com',
            departmentId: 'uuid-dept-1'
        );

        $this->assertSame('uuid-1', $entity->getId());
        $this->assertSame('x@example.com', $entity->getEmail());
        $this->assertSame('uuid-dept-1', $entity->getDepartmentId());
    }
}

