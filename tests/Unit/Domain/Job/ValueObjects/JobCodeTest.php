<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Job\ValueObjects;

use App\Domain\Job\ValueObjects\JobCode;
use InvalidArgumentException;
use Tests\TestCase;

class JobCodeTest extends TestCase
{
    public function test_from_string_accepts_customer_prefix_and_four_digits(): void
    {
        $code = JobCode::fromString('TST1C_0001', 'TST1C');

        $this->assertSame('TST1C_0001', $code->getValue());
        $this->assertSame('TST1C', $code->getCustomerCode());
        $this->assertSame(1, $code->getSequence());
    }

    public function test_from_string_trims_customer_code_padding(): void
    {
        $code = JobCode::fromString('TST1C_0003', '  TST1C  ');

        $this->assertSame('TST1C_0003', $code->getValue());
        $this->assertSame(3, $code->getSequence());
    }

    public function test_from_string_rejects_hyphen_separator(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JobCode::fromString('TST1C-0003', 'TST1C');
    }

    public function test_from_string_rejects_wrong_customer_prefix(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JobCode::fromString('OTH2C_0001', 'TST1C');
    }

    public function test_from_string_rejects_suffix_that_is_not_four_digits(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JobCode::fromString('TST1C_003', 'TST1C');
    }

    public function test_from_string_rejects_empty_customer_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JobCode::fromString('TST1C_0001', '   ');
    }

    public function test_from_sequence_pads_four_digits(): void
    {
        $code = JobCode::fromSequence('TST1C', 12);

        $this->assertSame('TST1C_0012', $code->getValue());
        $this->assertSame(12, $code->getSequence());
    }

    public function test_from_sequence_rejects_values_above_9999(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JobCode::fromSequence('TST1C', 10000);
    }

    public function test_try_sequence_returns_null_when_code_does_not_belong_to_customer(): void
    {
        $this->assertNull(JobCode::trySequence('OTHER_0099', 'TST1C'));
        $this->assertSame(9, JobCode::trySequence('TST1C_0009', 'TST1C'));
    }

    public function test_sequence_uses_customer_code_without_trailing_counter(): void
    {
        $first = JobCode::fromSequence('NCT001', 1);
        $second = JobCode::fromSequence('NCT', 2);
        $third = JobCode::fromSequence('NCT001', 3);

        $this->assertSame('NCT_0001', $first->getValue());
        $this->assertSame('NCT', $first->getCustomerCode());
        $this->assertSame('NCT_0002', $second->getValue());
        $this->assertSame('NCT_0003', $third->getValue());
        $this->assertSame(1, JobCode::trySequence('NCT_0001', 'NCT001'));
        $this->assertNull(JobCode::trySequence('NCT001_0001', 'NCT001'));

        $persisted = JobCode::fromPersisted('NCT001_0001');
        $this->assertSame('NCT001_0001', $persisted->getValue());
        $this->assertSame(1, $persisted->getSequence());
    }

    public function test_sequence_keeps_a_single_trailing_digit_in_the_customer_code(): void
    {
        $code = JobCode::fromSequence('C0T2', 1);

        $this->assertSame('C0T2_0001', $code->getValue());
    }
}
