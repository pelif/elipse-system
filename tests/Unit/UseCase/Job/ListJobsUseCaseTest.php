<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Job;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\ValueObjects\Document;
use App\Domain\Job\Entities\Job as JobEntity;
use App\Domain\Job\Repositories\JobRepositoryInterface;
use App\Domain\Job\ValueObjects\JobCode;
use App\UseCase\Job\ListJobsUseCase;
use Mockery;
use Tests\TestCase;

class ListJobsUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data_and_filters(): void
    {
        $customer = new Customer(
            id: '22222222-2222-2222-2222-222222222222',
            companyName: 'Cliente Alfa',
            legalName: 'Cliente Alfa LTDA',
            document: Document::fromPersisted('11222333000181'),
            codeCustomer: 'TST1C',
            active: true,
            deadlinePayment: null,
            antecipation: null,
            antecipationInstrutions: null,
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null,
            comissionedAgentId: null,
            comissionedAgent: null,
            financialInfo: null,
            contacts: [],
            relationShipsCounts: null,
        );

        $job = new JobEntity(
            customerId: '22222222-2222-2222-2222-222222222222',
            productId: '33333333-3333-3333-3333-333333333333',
            jobCode: JobCode::fromString('TST1C_0001', 'TST1C'),
            jobTitle: 'Lançamento',
            contractorId: '66666666-6666-6666-6666-666666666666',
            id: '11111111-1111-1111-1111-111111111111',
            customer: $customer,
            contractorName: 'Carla',
            osQuantity: 2,
        );

        $filters = ['job_title' => 'Lançamento', 'page' => 1];

        $repository = Mockery::mock(JobRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with($filters)->andReturn([
            'data' => [$job],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $output = (new ListJobsUseCase($repository))->execute($filters);

        $this->assertCount(1, $output['data']);
        $this->assertSame('Lançamento', $output['data'][0]->jobTitle);
        $this->assertSame('Cliente Alfa', $output['data'][0]->customerName);
        $this->assertSame('Carla', $output['data'][0]->contractorName);
        $this->assertSame(2, $output['data'][0]->osQuantity);
        $this->assertSame(10, $output['meta']['per_page']);
        $this->assertSame('Lançamento', $output['filters']['jobTitle']);
        $this->assertSame('updated_at', $output['filters']['sortField']);
        $this->assertSame('desc', $output['filters']['sortDirection']);
    }
}
