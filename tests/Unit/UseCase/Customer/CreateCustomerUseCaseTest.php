<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Customer;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\ValueObjects\Document;
use App\UseCase\Customer\CreateCustomerUseCase;
use App\UseCase\Customer\DTO\CreateCustomerInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateCustomerUseCaseTest extends TestCase
{
    private const VALID_CNPJ = '11222333000181';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $useCase = new CreateCustomerUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_creates_customer_and_returns_output_dto(): void
    {
        $input = new CreateCustomerInputDto(
            companyName: 'Nova Indústria',
            legalName: 'Nova Indústria SA',
            document: self::VALID_CNPJ,
            codeCustomer: 'CLI0000001'
        );

        $createdEntity = new Customer(
            id: '11111111-1111-1111-1111-111111111111',
            companyName: 'Nova Indústria',
            legalName: 'Nova Indústria SA',
            document: Document::fromString(self::VALID_CNPJ),
            codeCustomer: 'CLI0000001',
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

        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input) {
                return $entity instanceof Customer
                    && $entity->getId() === null
                    && $entity->getCompanyName() === $input->companyName
                    && $entity->getDocument()->getValue() === Document::fromString($input->document)->getValue();
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateCustomerUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertSame('11111111-1111-1111-1111-111111111111', $output->id);
        $this->assertSame('Nova Indústria', $output->companyName);
        $this->assertSame(self::VALID_CNPJ, $output->document);
    }
}
