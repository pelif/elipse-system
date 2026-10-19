<?php

namespace Tests\Unit\UseCase\Product;

use App\Domain\Product\Entities\Product as ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\ListProductsUseCase;
use Mockery;
use Tests\TestCase;

class ListProductsUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data(): void
    {
        $p = new ProductEntity(
            id: '77777777-7777-7777-7777-777777777777',
            name: 'Lista',
            description: null,
            createdAt: null,
            updatedAt: null
        );

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with([])->andReturn([
            'data' => [$p],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $useCase = new ListProductsUseCase($repository);
        $out = $useCase->execute([]);

        $this->assertCount(1, $out['data']);
        $this->assertSame('Lista', $out['data'][0]->name);
        $this->assertArrayHasKey('filters', $out);
    }
}
