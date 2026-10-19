<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ThirdParty\ValueObjects;

use App\Domain\ThirdParty\ValueObjects\ThirdPartyJobHistory;
use Tests\TestCase;

class ThirdPartyJobHistoryTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $history = new ThirdPartyJobHistory(4, 'JOB-0009');

        $this->assertSame(4, $history->getJobsCount());
        $this->assertSame('JOB-0009', $history->getLastJobCode());
    }

    public function test_zero_returns_empty_history(): void
    {
        $history = ThirdPartyJobHistory::zero();

        $this->assertSame(0, $history->getJobsCount());
        $this->assertNull($history->getLastJobCode());
    }
}
