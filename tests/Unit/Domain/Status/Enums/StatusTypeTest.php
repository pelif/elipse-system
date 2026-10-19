<?php

namespace Tests\Unit\Domain\Status\Enums;

use App\Domain\Status\Enums\StatusType;
use Tests\TestCase;

class StatusTypeTest extends TestCase
{
    public function test_job_and_order_values(): void
    {
        $this->assertSame(1, StatusType::JOB->value);
        $this->assertSame(2, StatusType::ORDER->value);
    }

    public function test_from_int(): void
    {
        $this->assertSame(StatusType::JOB, StatusType::from(1));
        $this->assertSame(StatusType::ORDER, StatusType::from(2));
    }
}

