<?php

namespace Tests\Integration\UseCase\Service;

use App\Infrastructure\Eloquent\Service\EloquentServiceRepository;
use App\Models\ServiceUnity;
use App\UseCase\Service\CreateServiceUseCase;
use App\UseCase\Service\DTO\CreateServiceInputDto;
use App\UseCase\Service\ListServicesUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListServicesUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private EloquentServiceRepository $repository;
    private ListServicesUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentServiceRepository();
        $this->useCase = new ListServicesUseCase($this->repository);
    }

    public function test_returns_paginated_services(): void
    {
        $unity = ServiceUnity::query()->create([
            'unity' => 'Hora',
        ]);

        $create = new CreateServiceUseCase($this->repository);
        $create->execute(new CreateServiceInputDto(
            service: 'Serviço A',
            unityId: (string) $unity->id,
            description: null
        ));
        $create->execute(new CreateServiceInputDto(
            service: 'Serviço B',
            unityId: (string) $unity->id,
            description: null
        ));

        $out = $this->useCase->execute([]);

        $this->assertCount(2, $out['data']);
        $this->assertArrayHasKey('meta', $out);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame(10, $out['meta']['per_page']);
        $this->assertSame('Hora', $out['data'][0]->unity);
        $this->assertSame('Hora', $out['data'][1]->unity);
    }

    public function test_filters_are_echoed_back(): void
    {
        $out = $this->useCase->execute(['service' => 'Foo', 'search' => 'Bar']);

        $this->assertSame('Foo', $out['filters']['service']);
        $this->assertSame('Bar', $out['filters']['search']);
    }
}
