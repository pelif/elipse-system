<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Job\ValueObjects;

use App\Domain\Job\ValueObjects\BriefingUrl;
use InvalidArgumentException;
use Tests\TestCase;

class BriefingUrlTest extends TestCase
{
    public function test_from_nullable_returns_null_for_empty_values(): void
    {
        $this->assertNull(BriefingUrl::fromNullable(null));
        $this->assertNull(BriefingUrl::fromNullable('   '));
    }

    public function test_from_string_accepts_http_and_https_with_a_dot(): void
    {
        $https = BriefingUrl::fromString('https://briefing.example.com/job');
        $http = BriefingUrl::fromString(' http://files.example.com/a.pdf ');

        $this->assertSame('https://briefing.example.com/job', $https->getValue());
        $this->assertSame('http://files.example.com/a.pdf', $http->getValue());
    }

    public function test_from_string_rejects_scheme_other_than_http(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BriefingUrl::fromString('ftp://files.example.com/a.pdf');
    }

    public function test_from_string_rejects_url_without_a_dot(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BriefingUrl::fromString('http://localhost/briefing');
    }

    public function test_from_string_rejects_value_longer_than_500_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BriefingUrl::fromString('https://example.com/'.str_repeat('a', 490));
    }
}
