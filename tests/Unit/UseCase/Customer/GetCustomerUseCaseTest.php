<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Customer;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\ValueObjects\Document;
use App\UseCase\Customer\GetCustomerUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class GetCustomerUseCaseTest extends TestCase
{
    private const VALID_CNPJ = '11222333000181';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_empty(): void
    {
        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $useCase = new GetCustomerUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with('22222222-2222-2222-2222-222222222222')->andReturn(null);

        $useCase = new GetCustomerUseCase($repository);
        $this->assertNull($useCase->execute('22222222-2222-2222-2222-222222222222'));
    }

    public function test_returns_output_when_found(): void
    {
        $entity = new Customer(
            id: '33333333-3333-3333-3333-333333333333',
            companyName: 'Cliente Exemplo',
            legalName: 'Cliente Exemplo ME',
            document: Document::fromString(self::VALID_CNPJ),
            codeCustomer: 'CDE0009999',
            active: false,
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

        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with('33333333-3333-3333-3333-333333333333')->andReturn($entity);

        $useCase = new GetCustomerUseCase($repository);
        $output = $useCase->execute('33333333-3333-3333-3333-333333333333');

        $this->assertNotNull($output);
        $this->assertSame('Cliente Exemplo', $output->companyName);
        $this->assertFalse($output->active);
    }
}
