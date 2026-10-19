<?php

namespace Tests\Unit\UseCase\Product;

use App\Domain\Product\Entities\Product as ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\GetProductUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class GetProductUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_empty(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $useCase = new GetProductUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with('22222222-2222-2222-2222-222222222222')->andReturn(null);

        $useCase = new GetProductUseCase($repository);
        $this->assertNull($useCase->execute('22222222-2222-2222-2222-222222222222'));
    }

    public function test_returns_output_when_found(): void
    {
        $entity = new ProductEntity(
            id: '33333333-3333-3333-3333-333333333333',
            name: 'Encontrado',
            description: 'D',
            createdAt: null,
            updatedAt: null
        );

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($entity);

        $useCase = new GetProductUseCase($repository);
        $output = $useCase->execute('33333333-3333-3333-3333-333333333333');

        $this->assertNotNull($output);
        $this->assertSame('Encontrado', $output->name);
    }
}
