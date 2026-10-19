<?php

namespace Tests\Unit\UseCase\Service;

use App\Domain\Service\Entities\Service as ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\ListServicesUseCase;
use Mockery;
use Tests\TestCase;

class ListServicesUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data(): void
    {
        $entity = new ServiceEntity(
            id: '77777777-7777-7777-7777-777777777777',
            service: 'Instalação',
            description: null,
            unityId: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            unity: 'Metro',
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        );

        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $repository
            ->shouldReceive('findAllPaginated')
            ->once()
            ->with([])
            ->andReturn([
                'data' => [$entity],
                'meta' => [
                    'current_page' => 1,
                    'last_page'    => 1,
                    'per_page'     => 10,
                    'total'        => 1,
                    'links'        => [],
                ],
            ]);

        $useCase = new ListServicesUseCase($repository);
        $out = $useCase->execute([]);

        $this->assertCount(1, $out['data']);
        $this->assertSame('Instalação', $out['data'][0]->service);
        $this->assertSame('dddddddd-dddd-dddd-dddd-dddddddddddd', $out['data'][0]->unityId);
        $this->assertSame('Metro', $out['data'][0]->unity);
        $this->assertArrayHasKey('filters', $out);
    }
}
