<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Contractor;

use App\Domain\Contractor\Entities\Contractor;
use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\Contractor\ListContractorsUseCase;
use Mockery;
use Tests\TestCase;

class ListContractorsUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data_filters_and_defaults(): void
    {
        $contractor = new Contractor(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            customerId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            name: 'Lista Contratante',
            phone: '11999990000',
            email: 'list@test.local',
            communicationCare: null,
            typeContact: 'Contractor',
            customerName: 'Cliente Co',
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $filters = [
            'name' => 'Contratante',
            'phone' => '99',
            'email' => 'e@',
            'customer' => 'Cli',
            'customerName' => 'CN',
            'company_name' => 'Legal Co',
            'communicationCare' => 'Care',
            'communication_care' => 'Alt',
            'page' => 1,
            'search' => 'glob',
            'sortField' => 'created_at',
            'sortDirection' => 'desc',
        ];

        $repository = Mockery::mock(ContractorRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with($filters)->andReturn([
            'data' => [$contractor],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $useCase = new ListContractorsUseCase($repository);
        $out = $useCase->execute($filters);

        $this->assertCount(1, $out['data']);
        $this->assertSame('Lista Contratante', $out['data'][0]->name);
        $this->assertSame('Cliente Co', $out['data'][0]->customerName);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame('Contratante', $out['filters']['name']);
        $this->assertSame('Legal Co', $out['filters']['company_name']);
        $this->assertSame('Care', $out['filters']['communicationCare']);
        $this->assertSame('Alt', $out['filters']['communication_care']);
        $this->assertSame('created_at', $out['filters']['sortField']);
        $this->assertSame('desc', $out['filters']['sortDirection']);
        $this->assertSame(10, $out['meta']['per_page']);
    }

    public function test_passes_empty_array_when_execute_without_args(): void
    {
        $repository = Mockery::mock(ContractorRepositoryInterface::class);
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

        $useCase = new ListContractorsUseCase($repository);
        $out = $useCase->execute();

        $this->assertSame([], $out['data']);
    }
}
