<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Customer;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\ValueObjects\Document;
use App\UseCase\Customer\DTO\UpdateCustomerInputDto;
use App\UseCase\Customer\UpdateCustomerUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateCustomerUseCaseTest extends TestCase
{
    private const VALID_CNPJ = '60701190000104';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $useCase = new UpdateCustomerUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_update_fails(): void
    {
        $input = new UpdateCustomerInputDto(
            id: '44444444-4444-4444-4444-444444444444',
            companyName: 'Nome',
            legalName: 'Razão',
            document: self::VALID_CNPJ,
            codeCustomer: 'ALT0004444'
        );

        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn(null);

        $useCase = new UpdateCustomerUseCase($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $input = new UpdateCustomerInputDto(
            id: '55555555-5555-5555-5555-555555555555',
            companyName: 'Atualizada',
            legalName: 'Razão atualizada',
            document: self::VALID_CNPJ,
            codeCustomer: 'ALT0005555'
        );

        $updated = new Customer(
            id: $input->id,
            companyName: 'Atualizada',
            legalName: 'Razão atualizada',
            document: Document::fromString(self::VALID_CNPJ),
            codeCustomer: 'ALT0005555',
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
        $repository->shouldReceive('update')->once()->andReturn($updated);

        $useCase = new UpdateCustomerUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Atualizada', $output->companyName);
    }
}
