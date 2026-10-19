<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Eloquent\ThirdParty;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\Entities\ThirdParty;
use App\Infrastructure\Eloquent\ThirdParty\EloquentThirdPartyRepository;
use App\Models\Job;
use App\Models\Service as ServiceModel;
use App\Models\ServiceThirdParty as ServiceThirdPartyModel;
use App\Models\ThirdParty as ThirdPartyModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentThirdPartyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private const CNPJ_A = '11222333000181';

    private const CNPJ_B = '00000000000191';

    private const CNPJ_C = '60701190000104';

    private EloquentThirdPartyRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentThirdPartyRepository;
    }

    private function makeEntity(
        ?string $id,
        string $name,
        ?string $alias,
        string $documentDigits,
        ?string $indication = null,
        bool $active = true,
        ?string $companyName = null,
        ?string $observation = null,
        string $email = 'mail@test.local',
        array $serviceIds = []
    ): ThirdParty {
        return new ThirdParty(
            id: $id,
            name: $name,
            alias: $alias,
            phone: '11999997777',
            phoneAlternative: null,
            email: $email,
            emailAlternative: null,
            indication: $indication,
            observation: $observation,
            companyName: $companyName,
            document: Document::fromString($documentDigits),
            active: $active,
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
            serviceIds: $serviceIds,
        );
    }

    public function test_create_assigns_uuid_and_persists_indication(): void
    {
        $entity = $this->makeEntity(
            id: null,
            name: 'Nova Terceira',
            alias: 'NT',
            documentDigits: self::CNPJ_A,
            indication: 'Parceiro A',
            companyName: 'Razão Nova',
            observation: 'Note',
            email: 'nova@test.local'
        );

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('Nova Terceira', $created->getName());
        $this->assertSame('Parceiro A', $created->getIndication());
        $this->assertSame(self::CNPJ_A, $created->getDocument()->getValue());

        $row = ThirdPartyModel::query()->find($created->getId());
        $this->assertNotNull($row);
        $this->assertSame('Parceiro A', $row->indication);
        $this->assertSame(self::CNPJ_A, $row->document);
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_update_and_delete_round_trip(): void
    {
        $created = $this->repository->create($this->makeEntity(
            id: null,
            name: 'Antes',
            alias: null,
            documentDigits: self::CNPJ_B,
        ));

        $updated = $this->repository->update($this->makeEntity(
            id: $created->getId(),
            name: 'Depois',
            alias: 'Alt',
            documentDigits: self::CNPJ_B,
            indication: 'Ind Alt',
            companyName: 'Co Alt',
            email: 'up@test.local'
        ));

        $this->assertNotNull($updated);
        $this->assertSame('Depois', $updated->getName());
        $this->assertSame('Ind Alt', $updated->getIndication());

        $this->assertTrue($this->repository->deleteById((string) $created->getId()));
        $this->assertNull(ThirdPartyModel::query()->find($created->getId()));
    }

    public function test_update_returns_null_when_id_blank(): void
    {
        $entity = $this->makeEntity(
            id: null,
            name: 'X',
            alias: null,
            documentDigits: self::CNPJ_A,
        );

        $this->assertNull($this->repository->update($entity));
    }

    public function test_find_all_paginated_filters_search_sort_and_second_page(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->repository->create($this->makeEntity(
                id: null,
                name: 'Zeta Marker '.$i,
                alias: 'al'.$i,
                documentDigits: self::CNPJ_A,
                indication: 'Group One',
                companyName: 'Empresa '.$i,
                observation: 'obs '.$i,
                email: sprintf('user%02d@test.local', $i),
            ));
        }

        $filters = ['name' => 'Marker'];
        $byName = $this->repository->findAllPaginated($filters);

        $this->assertSame(11, $byName['meta']['total']);
        $this->assertCount(10, $byName['data']);
        $this->assertSame(10, $byName['meta']['per_page']);
        $this->assertSame(2, $byName['meta']['last_page']);

        $page2 = $this->repository->findAllPaginated(['name' => 'Marker', 'page' => 2]);
        $this->assertCount(1, $page2['data']);

        $byIndication = $this->repository->findAllPaginated(['indication' => 'Group']);
        $this->assertSame(11, $byIndication['meta']['total']);

        $byEmail = $this->repository->findAllPaginated(['email' => 'user05@']);
        $this->assertCount(1, $byEmail['data']);

        $this->repository->create($this->makeEntity(
            id: null,
            name: 'Other Co',
            alias: 'solo',
            documentDigits: self::CNPJ_C,
            indication: null,
            companyName: 'FindMe Razão',
            email: 'other@test.local',
        ));

        $search = $this->repository->findAllPaginated(['search' => 'FindMe']);
        $this->assertCount(1, $search['data']);
        $this->assertSame('FindMe Razão', $search['data'][0]->getCompanyName());

        $this->repository->create($this->makeEntity(
            id: null,
            name: 'Inactive Row',
            alias: null,
            documentDigits: self::CNPJ_B,
            indication: null,
            active: false,
            email: 'off@test.local',
        ));

        $activeOnly = $this->repository->findAllPaginated(['active' => false]);
        $this->assertGreaterThanOrEqual(1, $activeOnly['meta']['total']);
        foreach ($activeOnly['data'] as $row) {
            $this->assertFalse($row->isActive());
        }

        $sortedDesc = $this->repository->findAllPaginated([
            'name' => 'Marker',
            'sortField' => 'created_at',
            'sortDirection' => 'desc',
        ]);
        $this->assertCount(10, $sortedDesc['data']);

        $docFilter = $this->repository->findAllPaginated([
            'document' => self::CNPJ_C,
        ]);
        $this->assertCount(1, $docFilter['data']);
        $this->assertSame('Other Co', $docFilter['data'][0]->getName());
    }

    public function test_find_all_paginated_unknown_sort_defaults_to_name(): void
    {
        $this->repository->create($this->makeEntity(null, 'Alpha', null, self::CNPJ_A, email: 'a1@test.local'));
        $this->repository->create($this->makeEntity(null, 'Beta', null, self::CNPJ_B, email: 'a2@test.local'));

        $result = $this->repository->findAllPaginated([
            'sortField' => 'illegal_column',
            'sortDirection' => 'desc',
        ]);

        $this->assertCount(2, $result['data']);
        $this->assertSame('Beta', $result['data'][0]->getName());
        $this->assertSame('Alpha', $result['data'][1]->getName());
    }

    public function test_create_persists_service_pivot_rows(): void
    {
        $serviceA = $this->createService('Áudio');
        $serviceB = $this->createService('Iluminação');

        $created = $this->repository->create($this->makeEntity(
            id: null,
            name: 'Técnico Pivot',
            alias: 'TP',
            documentDigits: self::CNPJ_A,
            email: 'pivot@test.local',
            serviceIds: [$serviceA, $serviceB],
        ));

        $createdIds = $created->getServiceIds();
        sort($createdIds);
        $expectedIds = [$serviceA, $serviceB];
        sort($expectedIds);
        $this->assertSame($expectedIds, $createdIds);
        $this->assertCount(2, $created->getServices());

        $pivots = ServiceThirdPartyModel::query()
            ->where('third_parties_id', $created->getId())
            ->orderBy('services_id')
            ->get();

        $this->assertCount(2, $pivots);
        foreach ($pivots as $pivot) {
            $this->assertSame($created->getId(), $pivot->third_parties_id);
        }
    }

    public function test_update_replaces_service_pivot_rows(): void
    {
        $serviceA = $this->createService('Áudio');
        $serviceB = $this->createService('Iluminação');
        $serviceC = $this->createService('Vídeo');

        $created = $this->repository->create($this->makeEntity(
            id: null,
            name: 'Técnico Sync',
            alias: null,
            documentDigits: self::CNPJ_B,
            email: 'sync@test.local',
            serviceIds: [$serviceA, $serviceB],
        ));

        $updated = $this->repository->update($this->makeEntity(
            id: $created->getId(),
            name: 'Técnico Sync',
            alias: null,
            documentDigits: self::CNPJ_B,
            email: 'sync@test.local',
            serviceIds: [$serviceC],
        ));

        $this->assertNotNull($updated);
        $this->assertSame([$serviceC], $updated->getServiceIds());
        $this->assertSame('Vídeo', $updated->getServices()[0]['service']);

        $pivots = ServiceThirdPartyModel::query()
            ->where('third_parties_id', $created->getId())
            ->get();
        $this->assertCount(1, $pivots);
        $this->assertSame($serviceC, $pivots[0]->services_id);
    }

    public function test_list_returns_services_and_job_history(): void
    {
        $serviceA = $this->createService('Áudio');
        $created = $this->repository->create($this->makeEntity(
            id: null,
            name: 'Histórico Terceiro',
            alias: 'HT',
            documentDigits: self::CNPJ_C,
            email: 'hist@test.local',
            serviceIds: [$serviceA],
        ));

        $olderJob = Job::forceCreate([
            'id' => (string) Str::uuid(),
            'job_code' => 'JOB-0001',
            'created_at' => '2026-01-01 10:00:00',
        ]);
        $newerJob = Job::forceCreate([
            'id' => (string) Str::uuid(),
            'job_code' => 'JOB-0009',
            'created_at' => '2026-03-01 10:00:00',
        ]);

        DB::table('job_budget_third_party')->insert([
            [
                'id' => (string) Str::uuid(),
                'job_id' => $olderJob->id,
                'third_party_id' => $created->getId(),
                'service_id' => $serviceA,
                'created_at' => '2026-01-01 10:00:00',
            ],
            [
                'id' => (string) Str::uuid(),
                'job_id' => $newerJob->id,
                'third_party_id' => $created->getId(),
                'service_id' => $serviceA,
                'created_at' => '2026-03-01 10:00:00',
            ],
            [
                'id' => (string) Str::uuid(),
                'job_id' => $newerJob->id,
                'third_party_id' => $created->getId(),
                'service_id' => $serviceA,
                'created_at' => '2026-03-01 11:00:00',
            ],
        ]);

        $result = $this->repository->findAllPaginated(['name' => 'Histórico Terceiro']);

        $this->assertCount(1, $result['data']);
        $row = $result['data'][0];
        $this->assertSame([$serviceA], $row->getServiceIds());
        $this->assertSame('Áudio', $row->getServices()[0]['service']);
        $this->assertSame(2, $row->getJobHistory()->getJobsCount());
        $this->assertSame('JOB-0009', $row->getJobHistory()->getLastJobCode());
    }

    private function createService(string $name): string
    {
        $model = ServiceModel::query()->create([
            'service' => $name,
            'description' => null,
        ]);

        return (string) $model->id;
    }
}
