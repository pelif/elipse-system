<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Entities\ResourceCategory;
use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\ListResourceCategoriesUseCase;
use Mockery;
use Tests\TestCase;

class ListResourceCategoriesUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data_filters_and_defaults(): void
    {
        $category = new ResourceCategory(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            name: 'Lista categoria',
            description: 'Alpha',
        );

        $filters = [
            'name' => 'Lista',
            'description' => 'Alp',
            'page' => 1,
            'search' => 'glob',
            'sortField' => 'name',
            'sortDirection' => 'desc',
        ];

        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with($filters)->andReturn([
            'data' => [$category],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $useCase = new ListResourceCategoriesUseCase($repository);
        $out = $useCase->execute($filters);

        $this->assertCount(1, $out['data']);
        $this->assertSame('Lista categoria', $out['data'][0]->name);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame('Lista', $out['filters']['name']);
        $this->assertSame('Alp', $out['filters']['description']);
        $this->assertSame('name', $out['filters']['sortField']);
        $this->assertSame('desc', $out['filters']['sortDirection']);
        $this->assertSame(10, $out['meta']['per_page']);
    }

    public function test_passes_empty_array_when_execute_without_args(): void
    {
        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with([])->andReturn([
            'data' => [],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 0,
                'links' => [],
            ],
        ]);

        $useCase = new ListResourceCategoriesUseCase($repository);
        $out = $useCase->execute();

        $this->assertSame([], $out['data']);
    }
}
