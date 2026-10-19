<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Job\Entities;

use App\Domain\Job\Entities\JobOrganizerLink;
use InvalidArgumentException;
use Tests\TestCase;

class JobOrganizerLinkTest extends TestCase
{
    public function test_getters_return_constructor_values(): void
    {
        $link = new JobOrganizerLink(
            jobId: ' 11111111-1111-1111-1111-111111111111 ',
            organizerId: ' 22222222-2222-2222-2222-222222222222 ',
            createdAt: '2026-09-29T14:00:00+00:00',
        );

        $this->assertSame('11111111-1111-1111-1111-111111111111', $link->getJobId());
        $this->assertSame('22222222-2222-2222-2222-222222222222', $link->getOrganizerId());
        $this->assertSame('2026-09-29T14:00:00+00:00', $link->getCreatedAt());
    }

    public function test_blank_created_at_becomes_null(): void
    {
        $link = new JobOrganizerLink(
            jobId: '11111111-1111-1111-1111-111111111111',
            organizerId: '22222222-2222-2222-2222-222222222222',
            createdAt: '   ',
        );

        $this->assertNull($link->getCreatedAt());
    }

    public function test_rejects_blank_job_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new JobOrganizerLink(
            jobId: '   ',
            organizerId: '22222222-2222-2222-2222-222222222222',
        );
    }

    public function test_rejects_blank_organizer_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new JobOrganizerLink(
            jobId: '11111111-1111-1111-1111-111111111111',
            organizerId: '   ',
        );
    }
}
