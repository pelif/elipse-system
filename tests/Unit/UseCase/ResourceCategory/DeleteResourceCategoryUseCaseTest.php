<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\DeleteResourceCategoryUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class DeleteResourceCategoryUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $useCase = new DeleteResourceCategoryUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_repository_result(): void
    {
        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $repository->shouldReceive('deleteById')->once()->with('99999999-9999-9999-9999-999999999999')->andReturn(true);

        $useCase = new DeleteResourceCategoryUseCase($repository);
        $this->assertTrue($useCase->execute('99999999-9999-9999-9999-999999999999'));
    }
}
