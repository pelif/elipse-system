<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Job\ValueObjects;

use App\Domain\Job\ValueObjects\OrderCode;
use InvalidArgumentException;
use Tests\TestCase;

class OrderCodeTest extends TestCase
{
    public function test_from_job_code_and_sequence_pads_two_digits(): void
    {
        $code = OrderCode::fromJobCodeAndSequence('TST1C_0001', 1);

        $this->assertSame('TST1C_0001_01', $code->getValue());
        $this->assertSame(1, $code->getSequence());
    }

    public function test_from_job_code_and_sequence_builds_the_third_order(): void
    {
        $code = OrderCode::fromJobCodeAndSequence('EXT_001', 3);

        $this->assertSame('EXT_001_03', $code->getValue());
        $this->assertSame(3, $code->getSequence());
    }

    public function test_from_job_code_and_sequence_grows_suffix_past_ninety_nine(): void
    {
        $code = OrderCode::fromJobCodeAndSequence('TST1C_0001', 100);

        $this->assertSame('TST1C_0001_100', $code->getValue());
    }

    public function test_from_job_code_and_sequence_trims_job_code(): void
    {
        $code = OrderCode::fromJobCodeAndSequence('  TST1C_0001  ', 2);

        $this->assertSame('TST1C_0001_02', $code->getValue());
    }

    public function test_from_job_code_and_sequence_rejects_empty_job_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OrderCode::fromJobCodeAndSequence('   ', 1);
    }

    public function test_from_job_code_and_sequence_rejects_sequence_below_one(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OrderCode::fromJobCodeAndSequence('TST1C_0001', 0);
    }

    public function test_from_job_code_and_sequence_rejects_value_longer_than_fifty_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OrderCode::fromJobCodeAndSequence(str_repeat('A', 48), 1);
    }
}
