<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Resource;

use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\DeleteResourceUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class DeleteResourceUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $useCase = new DeleteResourceUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_repository_result(): void
    {
        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $repository->shouldReceive('deleteById')->once()->with('99999999-9999-9999-9999-999999999999')->andReturn(true);

        $useCase = new DeleteResourceUseCase($repository);
        $this->assertTrue($useCase->execute('99999999-9999-9999-9999-999999999999'));
    }
}
