<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Entities\ResourceCategory;
use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\GetResourceCategoryUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class GetResourceCategoryUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_empty(): void
    {
        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $useCase = new GetResourceCategoryUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee')->andReturn(null);

        $useCase = new GetResourceCategoryUseCase($repository);
        $this->assertNull($useCase->execute('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee'));
    }

    public function test_returns_output_when_found(): void
    {
        $entity = new ResourceCategory(
            id: 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            name: 'Encontrada',
            description: 'Ver',
        );

        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($entity);

        $useCase = new GetResourceCategoryUseCase($repository);
        $output = $useCase->execute('ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertNotNull($output);
        $this->assertSame('Encontrada', $output->name);
        $this->assertSame('Ver', $output->description);
    }
}
