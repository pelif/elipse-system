<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Organizer;

use App\Domain\Organizer\Entities\Organizer;
use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\ListOrganizersUseCase;
use Mockery;
use Tests\TestCase;

class ListOrganizersUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data_filters_and_defaults(): void
    {
        $organizer = new Organizer(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            customerId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            name: 'Lista Organizadora',
            phone: '11999990000',
            email: 'list@test.local',
            communicationCare: null,
            observations: null,
            typeContact: 'Organizer',
            customerName: 'Cliente Co',
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $filters = [
            'name' => 'Organizadora',
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

        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with($filters)->andReturn([
            'data' => [$organizer],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $useCase = new ListOrganizersUseCase($repository);
        $out = $useCase->execute($filters);

        $this->assertCount(1, $out['data']);
        $this->assertSame('Lista Organizadora', $out['data'][0]->name);
        $this->assertSame('Cliente Co', $out['data'][0]->customerName);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame('Organizadora', $out['filters']['name']);
        $this->assertSame('Legal Co', $out['filters']['company_name']);
        $this->assertSame('Care', $out['filters']['communicationCare']);
        $this->assertSame('Alt', $out['filters']['communication_care']);
        $this->assertSame('created_at', $out['filters']['sortField']);
        $this->assertSame('desc', $out['filters']['sortDirection']);
        $this->assertSame(10, $out['meta']['per_page']);
    }

    public function test_passes_empty_array_when_execute_without_args(): void
    {
        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
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

        $useCase = new ListOrganizersUseCase($repository);
        $out = $useCase->execute();

        $this->assertSame([], $out['data']);
    }
}
