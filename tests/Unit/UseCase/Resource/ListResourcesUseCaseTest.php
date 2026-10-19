<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Resource;

use App\Domain\Resource\Entities\Resource;
use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\ListResourcesUseCase;
use Mockery;
use Tests\TestCase;

class ListResourcesUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data_filters_and_defaults(): void
    {
        $resource = new Resource(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            categoryId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            departmentId: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            resourceName: 'Lista item',
            patrimonialCode: 'P1',
            manufacturer: null,
            model: null,
            serialNumber: null,
            responsibleId: null,
            notes: null,
            documentationLink: null,
            createdAt: null,
            createdBy: null,
            updatedAt: null,
            updatedBy: null,
        );

        $filters = [
            'resource_name' => 'Lista',
            'patrimonial_code' => 'P',
            'page' => 1,
            'search' => 'glob',
            'sortField' => 'patrimonial_code',
            'sortDirection' => 'desc',
        ];

        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with($filters)->andReturn([
            'data' => [$resource],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $useCase = new ListResourcesUseCase($repository);
        $out = $useCase->execute($filters);

        $this->assertCount(1, $out['data']);
        $this->assertSame('Lista item', $out['data'][0]->resourceName);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame('Lista', $out['filters']['resource_name']);
        $this->assertSame('P', $out['filters']['patrimonial_code']);
        $this->assertSame('patrimonial_code', $out['filters']['sortField']);
        $this->assertSame('desc', $out['filters']['sortDirection']);
        $this->assertSame(10, $out['meta']['per_page']);
    }

    public function test_passes_empty_array_when_execute_without_args(): void
    {
        $repository = Mockery::mock(ResourceRepositoryInterface::class);
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

        $useCase = new ListResourcesUseCase($repository);
        $out = $useCase->execute();

        $this->assertSame([], $out['data']);
    }
}
