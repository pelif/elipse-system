<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Customer;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\ValueObjects\CustomerRelationshipsCounts;
use App\Domain\Customer\ValueObjects\Document;
use App\UseCase\Customer\ListCustomersUseCase;
use Mockery;
use Tests\TestCase;

class ListCustomersUseCaseTest extends TestCase
{
    private const VALID_CNPJ = '00000000000191';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_maps_paginated_data_filters_and_defaults(): void
    {
        $customer = new Customer(
            id: '77777777-7777-7777-7777-777777777777',
            companyName: 'Lista Cliente',
            legalName: 'Lista Cliente ME',
            document: Document::fromString(self::VALID_CNPJ),
            codeCustomer: 'LT00077111',
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
            relationShipsCounts: new CustomerRelationshipsCounts(3, 2, 1),
        );

        $filters = ['company_name' => 'Cliente', 'page' => 1];

        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $repository->shouldReceive('findAllPaginated')->once()->with($filters)->andReturn([
            'data' => [$customer],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'links' => [],
            ],
        ]);

        $useCase = new ListCustomersUseCase($repository);
        $out = $useCase->execute($filters);

        $this->assertCount(1, $out['data']);
        $this->assertSame('Lista Cliente', $out['data'][0]->companyName);
        $this->assertSame(3, $out['data'][0]->jobsCount);
        $this->assertSame(2, $out['data'][0]->contractorsCount);
        $this->assertSame(1, $out['data'][0]->organizersCount);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame('Cliente', $out['filters']['companyName']);
        $this->assertSame(10, $out['meta']['per_page']);
    }
}
