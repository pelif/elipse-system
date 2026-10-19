<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Organizer;

use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\DeleteOrganizerUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class DeleteOrganizerUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $useCase = new DeleteOrganizerUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_repository_result(): void
    {
        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $repository->shouldReceive('deleteById')->once()->with('99999999-9999-9999-9999-999999999999')->andReturn(true);

        $useCase = new DeleteOrganizerUseCase($repository);
        $this->assertTrue($useCase->execute('99999999-9999-9999-9999-999999999999'));
    }
}
