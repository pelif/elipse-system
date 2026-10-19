<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Eloquent\Organizer;

use App\Domain\Organizer\Entities\Organizer;
use App\Infrastructure\Eloquent\Organizer\EloquentOrganizerRepository;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentOrganizerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentOrganizerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentOrganizerRepository;
    }

    private function createCustomer(string $companyName, ?string $legalName = null): string
    {
        $customer = Customer::create([
            'company_name' => $companyName,
            'legal_name' => $legalName,
            'active' => true,
        ]);

        return (string) $customer->id;
    }

    private function createContractorContact(string $customerId, string $name, string $email): Contact
    {
        return Contact::query()->create([
            'customer_id' => $customerId,
            'name' => $name,
            'phone' => '11999990001',
            'email' => $email,
            'type_contact' => 'Contractor',
        ]);
    }

    private function makeEntity(
        ?string $id,
        string $name,
        string $phone,
        string $email,
        string $customerId,
        ?string $communicationCare = null,
        ?string $observations = null,
    ): Organizer {
        return new Organizer(
            id: $id,
            customerId: $customerId,
            name: $name,
            phone: $phone,
            email: $email,
            communicationCare: $communicationCare,
            observations: $observations,
            typeContact: null,
            customerName: null,
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );
    }

    public function test_create_assigns_uuid_and_sets_type_organizer(): void
    {
        $customerId = $this->createCustomer('Razão Grid Co', 'Legal Grid LTDA');

        $entity = $this->makeEntity(
            id: null,
            name: 'Novo Organizador',
            phone: '11988887777',
            email: 'nova@test.local',
            customerId: $customerId,
            communicationCare: 'Care',
            observations: 'Obs store',
        );

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Novo Organizador', $created->getName());
        $this->assertSame('Razão Grid Co', $created->getCustomerName());
        $this->assertSame('Organizer', $created->getTypeContact());

        $row = Contact::query()->find($created->getId());
        $this->assertNotNull($row);
        $this->assertSame('Organizer', $row->type_contact);
        $this->assertSame($customerId, $row->customer_id);
        $this->assertSame('Care', $row->communication_care);
        $this->assertSame('Obs store', $row->observations);
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_find_by_id_returns_null_for_non_organizer_contact(): void
    {
        $customerId = $this->createCustomer('Cliente Org');
        $contractor = $this->createContractorContact($customerId, 'Só Contratante', 'cont.only@test.local');

        $found = $this->repository->findById((string) $contractor->id);
        $this->assertNull($found);
    }

    public function test_update_and_delete_round_trip(): void
    {
        $customerId = $this->createCustomer('Cliente Up', 'Legal Up');

        $created = $this->repository->create($this->makeEntity(
            id: null,
            name: 'Antes',
            phone: '111',
            email: 'antes@test.local',
            customerId: $customerId,
        ));

        $updated = $this->repository->update($this->makeEntity(
            id: $created->getId(),
            name: 'Depois',
            phone: '222',
            email: 'depois@test.local',
            customerId: $customerId,
            communicationCare: 'Care alt',
            observations: 'Obs alt',
        ));

        $this->assertNotNull($updated);
        $this->assertSame('Depois', $updated->getName());
        $this->assertSame('Care alt', $updated->getCommunicationCare());
        $this->assertSame('Obs alt', $updated->getObservations());
        $this->assertSame('Cliente Up', $updated->getCustomerName());

        $this->assertTrue($this->repository->deleteById((string) $created->getId()));
        $this->assertNull(Contact::query()->find($created->getId()));
    }

    public function test_update_returns_null_when_id_blank(): void
    {
        $entity = $this->makeEntity(
            id: null,
            name: 'X',
            phone: '1',
            email: 'x@test.local',
            customerId: $this->createCustomer('C'),
        );

        $this->assertNull($this->repository->update($entity));
    }

    public function test_find_all_paginated_excludes_non_organizer_contacts(): void
    {
        $customerId = $this->createCustomer('Empresa Mista', 'Legal Mista');

        $this->repository->create($this->makeEntity(null, 'Organizador Um', '1', 'o1@test.local', $customerId));
        $this->repository->create($this->makeEntity(null, 'Organizador Dois', '2', 'o2@test.local', $customerId));
        $this->createContractorContact($customerId, 'Contratante Oculto', 'hidden@test.local');

        $result = $this->repository->findAllPaginated([]);

        $this->assertSame(2, $result['meta']['total']);
        $names = array_map(static fn (Organizer $o) => $o->getName(), $result['data']);
        $this->assertContains('Organizador Um', $names);
        $this->assertContains('Organizador Dois', $names);
        $this->assertNotContains('Contratante Oculto', $names);
    }

    public function test_find_all_paginated_filters_by_name_and_customer_company_name(): void
    {
        $customerAlpha = $this->createCustomer('Alpha Indústria');
        $customerBeta = $this->createCustomer('Beta Comércio');

        $this->repository->create($this->makeEntity(null, 'João Alpha', '1', 'a1@test.local', $customerAlpha));
        $this->repository->create($this->makeEntity(null, 'Maria Beta', '2', 'b1@test.local', $customerBeta));

        $byName = $this->repository->findAllPaginated(['name' => 'Alpha']);
        $this->assertSame(1, $byName['meta']['total']);
        $this->assertSame('João Alpha', $byName['data'][0]->getName());

        $byCustomer = $this->repository->findAllPaginated(['customer' => 'Beta']);
        $this->assertSame(1, $byCustomer['meta']['total']);
        $this->assertSame('Maria Beta', $byCustomer['data'][0]->getName());
        $this->assertSame('Beta Comércio', $byCustomer['data'][0]->getCustomerName());
    }

    public function test_find_all_paginated_filters_by_observations(): void
    {
        $customerId = $this->createCustomer('Obs Co');
        $this->repository->create($this->makeEntity(null, 'A', '1', 'a@test.local', $customerId, null, 'alpha note'));
        $this->repository->create($this->makeEntity(null, 'B', '2', 'b@test.local', $customerId, null, 'beta note'));

        $byObs = $this->repository->findAllPaginated(['observations' => 'alpha']);
        $this->assertSame(1, $byObs['meta']['total']);
        $this->assertSame('A', $byObs['data'][0]->getName());
    }

    public function test_find_all_paginated_search_and_second_page(): void
    {
        $customerId = $this->createCustomer('Busca Cliente SA');

        for ($i = 1; $i <= 11; $i++) {
            $this->repository->create($this->makeEntity(
                id: null,
                name: 'Marker Organizador '.$i,
                phone: '1199999'.sprintf('%04d', $i),
                email: sprintf('busca%02d@test.local', $i),
                customerId: $customerId,
                communicationCare: 'care marker '.$i,
                observations: 'obs marker '.$i,
            ));
        }

        $byMarker = $this->repository->findAllPaginated(['name' => 'Marker']);
        $this->assertSame(11, $byMarker['meta']['total']);
        $this->assertCount(10, $byMarker['data']);
        $this->assertSame(2, $byMarker['meta']['last_page']);

        $page2 = $this->repository->findAllPaginated(['name' => 'Marker', 'page' => 2]);
        $this->assertCount(1, $page2['data']);

        $search = $this->repository->findAllPaginated(['search' => 'Busca Cliente']);
        $this->assertSame(11, $search['meta']['total']);

        $searchObs = $this->repository->findAllPaginated(['search' => 'obs marker 3']);
        $this->assertSame(1, $searchObs['meta']['total']);
    }

    public function test_find_all_paginated_unknown_sort_defaults_to_name(): void
    {
        $cid = $this->createCustomer('Sort Co');
        $this->repository->create($this->makeEntity(null, 'Alpha Organizer', '1', 'a@test.local', $cid));
        $this->repository->create($this->makeEntity(null, 'Beta Organizer', '2', 'b@test.local', $cid));

        $result = $this->repository->findAllPaginated([
            'sortField' => 'illegal_column',
            'sortDirection' => 'desc',
        ]);

        $this->assertCount(2, $result['data']);
        $this->assertSame('Beta Organizer', $result['data'][0]->getName());
        $this->assertSame('Alpha Organizer', $result['data'][1]->getName());
    }

    public function test_delete_by_id_returns_false_for_contractor_contact(): void
    {
        $customerId = $this->createCustomer('Del Co');
        $contractor = $this->createContractorContact($customerId, 'Não apagar contractor', 'no.del@test.local');

        $this->assertFalse($this->repository->deleteById((string) $contractor->id));
        $this->assertNotNull(Contact::query()->find($contractor->id));
    }
}
