<?php

declare(strict_types=1);

namespace Tests\Integration\UseCase\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\UseCase\Customer\CreateCustomerUseCase;
use App\UseCase\Customer\DeleteCustomerUseCase;
use App\UseCase\Customer\DTO\CreateCustomerInputDto;
use App\UseCase\Customer\DTO\CustomerContactInputDto;
use App\UseCase\Customer\DTO\CustomerFinancialInfoInputDto;
use App\UseCase\Customer\DTO\UpdateCustomerInputDto;
use App\UseCase\Customer\GetCustomerUseCase;
use App\UseCase\Customer\ListCustomersUseCase;
use App\UseCase\Customer\UpdateCustomerUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private CustomerRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerRepositoryInterface::class);
    }

    public function test_create_throws_for_invalid_input(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new CreateCustomerUseCase($this->repository))->execute('bad');
    }

    public function test_full_flow_create_get_update_list_delete(): void
    {
        $input = new CreateCustomerInputDto(
            companyName: 'Flow Corp',
            legalName: 'Flow Corp SA',
            document: '04152201000165',
            codeCustomer: 'FLW0000001',
            financialInfo: new CustomerFinancialInfoInputDto(
                deadlinePayment: 12,
                antecipation: 2,
                antecipationInstructions: 'PIX',
                emailNf: 'flow-nf@tests.local'
            ),
            contacts: [
                new CustomerContactInputDto(name: 'Desk', email: 'desk@tests.local', typeContact: 'Ops'),
            ]
        );

        $created = (new CreateCustomerUseCase($this->repository))->execute($input);

        $this->assertNotEmpty($created->id);
        $this->assertSame('Flow Corp', $created->companyName);
        $this->assertSame('04152201000165', $created->document);
        $this->assertNotNull($created->financialInfo);
        $this->assertSame('flow-nf@tests.local', $created->financialInfo->emailNf);
        $this->assertCount(1, $created->contacts);

        $got = (new GetCustomerUseCase($this->repository))->execute($created->id);
        $this->assertNotNull($got);
        $this->assertSame($created->id, $got->id);

        $contactId = $created->contacts[0]->id;
        $this->assertNotEmpty($contactId);

        $updateDto = new UpdateCustomerInputDto(
            id: $created->id,
            companyName: 'Flow Corp Updated',
            legalName: 'Flow Corp SA',
            document: '04152201000165',
            codeCustomer: 'FLW0000001',
            active: false,
            financialInfo: new CustomerFinancialInfoInputDto(
                deadlinePayment: 20,
                emailNf: 'new-nf@tests.local'
            ),
            contacts: [
                new CustomerContactInputDto(
                    id: $contactId,
                    name: 'Desk Renamed',
                    email: 'desk2@tests.local',
                    typeContact: 'Ops'
                ),
            ]
        );

        $updated = (new UpdateCustomerUseCase($this->repository))->execute($updateDto);

        $this->assertNotNull($updated);
        $this->assertSame('Flow Corp Updated', $updated->companyName);
        $this->assertFalse($updated->active);
        $this->assertSame('Desk Renamed', $updated->contacts[0]->name);

        $listed = (new ListCustomersUseCase($this->repository))->execute(['search' => 'Flow Corp Updated']);
        $this->assertGreaterThanOrEqual(1, count($listed['data']));
        $match = array_filter(
            $listed['data'],
            static fn ($dto): bool => $dto->id === $created->id
        );
        $this->assertCount(1, $match);
        $listedCustomer = array_values($match)[0];
        $this->assertSame(0, $listedCustomer->jobsCount);
        $this->assertSame(0, $listedCustomer->contractorsCount);
        $this->assertSame(0, $listedCustomer->organizersCount);

        $deleted = (new DeleteCustomerUseCase($this->repository))->execute($created->id);
        $this->assertTrue($deleted);
        $this->assertNull((new GetCustomerUseCase($this->repository))->execute($created->id));
    }

    public function test_get_returns_null_for_missing_id(): void
    {
        $out = (new GetCustomerUseCase($this->repository))->execute('00000000-0000-0000-0000-000000000000');

        $this->assertNull($out);
    }

    public function test_delete_throws_for_empty_input(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DeleteCustomerUseCase($this->repository))->execute('');
    }

    public function test_delete_throws_for_null_input(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DeleteCustomerUseCase($this->repository))->execute(null);
    }

    public function test_list_maps_filters_into_response_structure(): void
    {
        (new CreateCustomerUseCase($this->repository))->execute(new CreateCustomerInputDto(
            companyName: 'List Filter Co',
            legalName: 'List Filter LTDA',
            document: '11222333000181',
            codeCustomer: 'LSTFILTER1'
        ));

        $out = (new ListCustomersUseCase($this->repository))->execute([
            'company_name' => 'Filter',
            'sortField' => 'company_name',
            'sortDirection' => 'asc',
        ]);

        $this->assertArrayHasKey('meta', $out);
        $this->assertArrayHasKey('filters', $out);
        $this->assertSame('Filter', $out['filters']['companyName']);
        $this->assertGreaterThanOrEqual(1, count($out['data']));
        $this->assertSame(0, $out['data'][0]->jobsCount);
        $this->assertSame(0, $out['data'][0]->contractorsCount);
        $this->assertSame(0, $out['data'][0]->organizersCount);
    }
}
