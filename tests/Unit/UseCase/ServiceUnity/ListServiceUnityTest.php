<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Entities\ServiceUnity;
use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\ListServiceUnity;
use DateTimeImmutable;
use Mockery;
use Tests\TestCase;

class ListServiceUnityTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function tests_paginated_with_filters(): void
    {
        $serviceUnity = new ServiceUnity(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            unity: 'List Item 001',
            createdBy: 5,
            updatedBy: 1,
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable()
        );

        $filters = [
            'unity' => 'List Item 001',
            'page' => 1,
            'search' => 'glob',
            'sortField' => 'unity',
            'sortDirection' => 'desc',
        ];

        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')
            ->once()
            ->with($filters)
            ->andReturn([
                'data' => [$serviceUnity],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 1,
                    'links' => [],
                ],
            ]);

        $useCase = new ListServiceUnity($repository);
        $out = $useCase->execute($filters);

        $this->assertCount(1, $out['data']);
        $this->assertSame('List Item 001', $out['data'][0]->unity);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame('unity', $out['filters']['sortField']);
        $this->assertSame('desc', $out['filters']['sortDirection']);
        $this->assertSame(10, $out['meta']['per_page']);
    }

    public function test_without_args_and_empty_arrays(): void
    {
        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $repository
            ->shouldReceive('findAllPaginated')
            ->once()
            ->with([])
            ->andReturn([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'links' => []
                ],
            ]);

        $useCase = new ListServiceUnity($repository);
        $out = $useCase->execute();

        $this->assertSame([], $out['data']);
    }


}
