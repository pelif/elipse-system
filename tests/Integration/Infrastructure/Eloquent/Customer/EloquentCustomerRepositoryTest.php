<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Eloquent\Customer;

use App\Domain\Customer\Entities\ContactEntity;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Entities\FinancialInfoEntity;
use App\Domain\Customer\ValueObjects\CustomerRelationshipsCounts;
use App\Domain\Customer\ValueObjects\Document;
use App\Infrastructure\Eloquent\Customer\EloquentCustomerRepository;
use App\Models\ComissionedAgent as ComissionedAgentModel;
use App\Models\Contact as ContactModel;
use App\Models\Customer as CustomerModel;
use App\Models\FinancialInfo as FinancialInfoModel;
use App\Models\Job as JobModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentCustomerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_CNPJ = '11222333000181';

    private EloquentCustomerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentCustomerRepository;
    }

    public function test_create_persist_customer_financial_and_contacts(): void
    {
        $customer = new Customer(
            id: null,
            companyName: 'ACME Repo',
            legalName: 'ACME Repo LTDA',
            document: Document::fromString(self::VALID_CNPJ),
            codeCustomer: 'ACME000010',
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
            financialInfo: new FinancialInfoEntity(
                id: null,
                customerId: null,
                deadlinePayment: 45,
                antecipation: 10,
                antecipationInstructions: 'Wire only',
                createdBy: null,
                updatedBy: null,
                createdAt: null,
                updatedAt: null,
                emailNf: 'nf-repo@tests.local',
                financialContactName: 'Finance Repo Contact',
                financialContactPhone: '11988887777',
                financialContactEmail: 'finance-repo@tests.local',
            ),
            contacts: [
                new ContactEntity(
                    id: null,
                    customerId: null,
                    name: 'Repo Contact',
                    phone: null,
                    email: 'c@tests.local',
                    typeContact: 'Billing',
                    createdBy: null,
                    updatedBy: null,
                    createdAt: null,
                    updatedAt: null
                ),
            ],
            relationShipsCounts: null,
        );

        $created = $this->repository->create($customer);

        $this->assertNotEmpty($created->getId());
        $this->assertSame('ACME Repo', $created->getCompanyName());
        $this->assertSame(self::VALID_CNPJ, $created->getDocument()->getValue());
        $this->assertNotNull($created->getFinancialInfo());
        $this->assertSame('nf-repo@tests.local', $created->getFinancialInfo()->getEmailNf());
        $this->assertSame(45, $created->getFinancialInfo()->getDeadlinePayment());
        $this->assertSame('Finance Repo Contact', $created->getFinancialInfo()->getFinancialContactName());
        $this->assertSame('11988887777', $created->getFinancialInfo()->getFinancialContactPhone());
        $this->assertSame('finance-repo@tests.local', $created->getFinancialInfo()->getFinancialContactEmail());
        $this->assertCount(1, $created->getContacts());

        $row = CustomerModel::query()->with(['financialInfo', 'contacts'])->find($created->getId());
        $this->assertNotNull($row);
        $this->assertNotNull($row->financialInfo);
        $this->assertSame('nf-repo@tests.local', $row->financialInfo->email_nf);
        $this->assertSame('Finance Repo Contact', $row->financialInfo->financial_contact_name);
        $this->assertSame('11988887777', $row->financialInfo->financial_contact_phone);
        $this->assertSame('finance-repo@tests.local', $row->financialInfo->financial_contact_email);
        $this->assertCount(1, $row->contacts);
    }

    public function test_find_by_id_returns_aggregate(): void
    {
        $created = $this->repository->create($this->minimalCustomer('Find Me', 'FM00009901', '60701190000104'));

        $found = $this->repository->findById((string) $created->getId());

        $this->assertNotNull($found);
        $this->assertSame($created->getId(), $found->getId());
        $this->assertSame('Find Me', $found->getCompanyName());
    }

    public function test_update_persists_core_fields_and_syncs_related_rows(): void
    {
        $created = $this->repository->create($this->minimalCustomer(
            'Before SA',
            'BF00008802',
            '00000000000191',
            contacts: [
                new ContactEntity(
                    id: null,
                    customerId: null,
                    name: 'Old Contact',
                    phone: null,
                    email: 'old@test.local',
                    typeContact: null,
                    createdBy: null,
                    updatedBy: null,
                    createdAt: null,
                    updatedAt: null
                ),
            ]
        ));

        $contactId = $created->getContacts()[0]->getId();
        $this->assertNotNull($contactId);

        $toUpdate = new Customer(
            id: $created->getId(),
            companyName: 'After SA',
            legalName: 'After Razão',
            document: Document::fromString('00000000000191'),
            codeCustomer: 'AF00008802',
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
            financialInfo: new FinancialInfoEntity(
                id: null,
                customerId: null,
                deadlinePayment: 21,
                antecipation: 3,
                antecipationInstructions: 'Updated instructions',
                createdBy: null,
                updatedBy: null,
                createdAt: null,
                updatedAt: null,
                emailNf: 'updated-nf@test.local'
            ),
            contacts: [
                new ContactEntity(
                    id: $contactId,
                    customerId: $created->getId(),
                    name: 'New Contact Label',
                    phone: '+5511999998888',
                    email: 'new@test.local',
                    typeContact: null,
                    createdBy: null,
                    updatedBy: null,
                    createdAt: null,
                    updatedAt: null
                ),
            ],
            relationShipsCounts: null,
        );

        $updated = $this->repository->update($toUpdate);

        $this->assertNotNull($updated);
        $this->assertSame('After SA', $updated->getCompanyName());
        $this->assertFalse($updated->isActive());
        $this->assertSame('New Contact Label', $updated->getContacts()[0]->getName());
        $this->assertSame(21, $updated->getFinancialInfo()?->getDeadlinePayment());

        $fromDb = CustomerModel::query()->find($created->getId());
        $this->assertSame('After SA', $fromDb->company_name);
        $this->assertSame(1, ContactModel::query()->where('customer_id', $created->getId())->count());
    }

    public function test_update_preserves_financial_row_when_payload_has_no_financial(): void
    {
        $created = $this->repository->create($this->minimalCustomer(
            'Keep Fin SA',
            'KF00005501',
            '60701190000104',
            financial: new FinancialInfoEntity(
                id: null,
                customerId: null,
                deadlinePayment: 30,
                antecipation: 1,
                antecipationInstructions: 'Keep me',
                createdBy: null,
                updatedBy: null,
                createdAt: null,
                updatedAt: null,
                emailNf: 'keep@tests.local'
            ),
        ));

        $customerId = (string) $created->getId();
        $financialId = $created->getFinancialInfo()?->getId();
        $this->assertNotNull($financialId);

        $toUpdate = new Customer(
            id: $customerId,
            companyName: 'Keep Fin SA Updated',
            legalName: 'Keep Fin LTDA',
            document: Document::fromString('60701190000104'),
            codeCustomer: 'KF00005501',
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

        $updated = $this->repository->update($toUpdate);

        $this->assertNotNull($updated);
        $this->assertSame('Keep Fin SA Updated', $updated->getCompanyName());
        $this->assertNotNull($updated->getFinancialInfo());
        $this->assertSame(30, $updated->getFinancialInfo()?->getDeadlinePayment());
        $this->assertSame('keep@tests.local', $updated->getFinancialInfo()?->getEmailNf());

        $row = FinancialInfoModel::query()->where('customer_id', $customerId)->first();
        $this->assertNotNull($row);
        $this->assertSame($financialId, $row->id);
    }

    public function test_delete_removes_customer_contacts_and_financial_info(): void
    {
        $created = $this->repository->create($this->minimalCustomer(
            'Delete Co',
            'DL00007703',
            '04252011000110',
            financial: new FinancialInfoEntity(
                id: null,
                customerId: null,
                deadlinePayment: 15,
                antecipation: null,
                antecipationInstructions: null,
                createdBy: null,
                updatedBy: null,
                createdAt: null,
                updatedAt: null,
                emailNf: null
            ),
            contacts: [
                new ContactEntity(
                    id: null,
                    customerId: null,
                    name: 'To delete',
                    phone: null,
                    email: null,
                    typeContact: null,
                    createdBy: null,
                    updatedBy: null,
                    createdAt: null,
                    updatedAt: null
                ),
            ]
        ));

        $id = (string) $created->getId();
        $this->assertTrue($this->repository->deleteById($id));

        $this->assertNull(CustomerModel::query()->find($id));
        $this->assertSame(0, FinancialInfoModel::query()->where('customer_id', $id)->count());
        $this->assertSame(0, ContactModel::query()->where('customer_id', $id)->count());
    }

    public function test_create_links_comissioned_agent_to_customer(): void
    {
        $agent = $this->createComissionedAgent();

        $customer = $this->minimalCustomer(
            'Agent Link Co',
            'AL00001001',
            '60701190000104',
            comissionedAgentId: (string) $agent->id,
        );

        $created = $this->repository->create($customer);

        $this->assertSame((string) $agent->id, $created->getComissionedAgentId());
        $this->assertNotNull($created->getComissionedAgent());
        $this->assertSame('Agente Teste', $created->getComissionedAgent()->getName());

        $agent->refresh();
        $this->assertSame($created->getId(), $agent->customer_id);

        $found = $this->repository->findById((string) $created->getId());
        $this->assertNotNull($found);
        $this->assertSame((string) $agent->id, $found->getComissionedAgentId());
        $this->assertNotNull($found->getComissionedAgent());
        $this->assertSame('Agente Teste', $found->getComissionedAgent()->getName());
    }

    public function test_update_changes_comissioned_agent_link(): void
    {
        $firstAgent = $this->createComissionedAgent();
        $secondAgent = $this->createComissionedAgent();

        $created = $this->repository->create($this->minimalCustomer(
            'Switch Agent Co',
            'SA00002001',
            '04152201000165',
            comissionedAgentId: (string) $firstAgent->id,
        ));

        $customerId = (string) $created->getId();

        $toUpdate = new Customer(
            id: $customerId,
            companyName: 'Switch Agent Co',
            legalName: 'Switch Agent Co LTDA',
            document: Document::fromString('04152201000165'),
            codeCustomer: 'SA00002001',
            active: true,
            deadlinePayment: null,
            antecipation: null,
            antecipationInstrutions: null,
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null,
            comissionedAgentId: (string) $secondAgent->id,
            comissionedAgent: null,
            financialInfo: null,
            contacts: [],
            relationShipsCounts: null,
        );

        $updated = $this->repository->update($toUpdate);

        $this->assertNotNull($updated);
        $this->assertSame((string) $secondAgent->id, $updated->getComissionedAgentId());

        $firstAgent->refresh();
        $secondAgent->refresh();

        $this->assertNull($firstAgent->customer_id);
        $this->assertSame($customerId, $secondAgent->customer_id);
    }

    public function test_update_unlinks_comissioned_agent_when_null(): void
    {
        $agent = $this->createComissionedAgent();

        $created = $this->repository->create($this->minimalCustomer(
            'Unlink Agent Co',
            'UA00003001',
            '04252011000110',
            comissionedAgentId: (string) $agent->id,
        ));

        $customerId = (string) $created->getId();

        $toUpdate = new Customer(
            id: $customerId,
            companyName: 'Unlink Agent Co',
            legalName: 'Unlink Agent Co LTDA',
            document: Document::fromString('04252011000110'),
            codeCustomer: 'UA00003001',
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

        $updated = $this->repository->update($toUpdate);

        $this->assertNotNull($updated);
        $this->assertNull($updated->getComissionedAgentId());

        $agent->refresh();
        $this->assertNull($agent->customer_id);
    }

    public function test_find_all_paginated_search_matches_company_or_document_digits(): void
    {
        $this->repository->create($this->minimalCustomer(
            'Unique Alpha Marker',
            'PG00006601',
            '04152201000165'
        ));
        $this->repository->create($this->minimalCustomer(
            'Other Beta Marker',
            'PG00006602',
            self::VALID_CNPJ
        ));

        $byName = $this->repository->findAllPaginated(['search' => 'Alpha']);
        $this->assertCount(1, $byName['data']);
        $this->assertSame('Unique Alpha Marker', $byName['data'][0]->getCompanyName());
        $this->assertSame(10, $byName['meta']['per_page']);

        $digits = '04152201000165';
        $byDocDigits = $this->repository->findAllPaginated(['search' => $digits]);
        $this->assertCount(1, $byDocDigits['data']);
        $this->assertStringContainsString($digits, $byDocDigits['data'][0]->getDocument()->getValue());
    }

    public function test_find_all_paginated_returns_relationship_counts(): void
    {
        $created = $this->repository->create($this->minimalCustomer(
            'Counts Marker Co',
            'CNT00001001',
            '60701190000104'
        ));

        $customerId = (string) $created->getId();

        for ($i = 0; $i < 3; $i++) {
            JobModel::query()->create([
                'id' => (string) Str::uuid(),
                'customer_id' => $customerId,
            ]);
        }

        ContactModel::query()->create([
            'id' => (string) Str::uuid(),
            'customer_id' => $customerId,
            'name' => 'Contractor A',
            'phone' => '11999990001',
            'email' => 'contractor-a@tests.local',
            'type_contact' => 'Contractor',
        ]);

        ContactModel::query()->create([
            'id' => (string) Str::uuid(),
            'customer_id' => $customerId,
            'name' => 'Contractor B',
            'phone' => '11999990002',
            'email' => 'contractor-b@tests.local',
            'type_contact' => 'Contractor',
        ]);

        ContactModel::query()->create([
            'id' => (string) Str::uuid(),
            'customer_id' => $customerId,
            'name' => 'Organizer A',
            'phone' => '11999990003',
            'email' => 'organizer-a@tests.local',
            'type_contact' => 'Organizer',
        ]);

        $result = $this->repository->findAllPaginated(['search' => 'Counts Marker']);

        $this->assertCount(1, $result['data']);

        $row = $result['data'][0];
        $this->assertSame('Counts Marker Co', $row->getCompanyName());

        $counts = $row->getRelationShipsCounts();
        $this->assertInstanceOf(CustomerRelationshipsCounts::class, $counts);
        $this->assertSame(3, $counts->getJobs());
        $this->assertSame(2, $counts->getContractors());
        $this->assertSame(1, $counts->getOrganizers());
    }

    /**
     * @param  array<int, ContactEntity>  $contacts
     */
    private function minimalCustomer(
        string $companyName,
        string $codeCustomer,
        string $cnpjDigits,
        ?FinancialInfoEntity $financial = null,
        array $contacts = [],
        ?string $comissionedAgentId = null,
    ): Customer {
        return new Customer(
            id: null,
            companyName: $companyName,
            legalName: $companyName.' LTDA',
            document: Document::fromString($cnpjDigits),
            codeCustomer: $codeCustomer,
            active: true,
            deadlinePayment: null,
            antecipation: null,
            antecipationInstrutions: null,
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null,
            comissionedAgentId: $comissionedAgentId,
            comissionedAgent: null,
            financialInfo: $financial,
            contacts: $contacts,
            relationShipsCounts: null,
        );
    }

    private function createComissionedAgent(): ComissionedAgentModel
    {
        return ComissionedAgentModel::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'Agente Teste',
            'default_comission' => 10.0,
            'payment_days' => 30,
            'active' => true,
        ]);
    }
}
