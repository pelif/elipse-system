<?php

namespace Tests\Integration\Infrastructure\Eloquent\EmailConfig;

use App\Domain\EmailConfig\Entities\EmailConfig as EmailConfigEntity;
use App\Infrastructure\Eloquent\EmailConfig\EloquentEmailConfigRepository;
use App\Models\Department\Department;
use App\Models\EmailSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentEmailConfigRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentEmailConfigRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentEmailConfigRepository();
    }

    public function test_create_email_setting(): void
    {
        $department = Department::query()->create(['department' => 'Teste']);

        $entity = new EmailConfigEntity(
            id: null,
            email: 'teste@example.com',
            departmentId: (string) $department->id
        );

        $created = $this->repository->create($entity);

        $this->assertNotNull($created->getId());
        $this->assertSame('teste@example.com', $created->getEmail());
        $this->assertSame((string) $department->id, $created->getDepartmentId());

        $row = EmailSetting::query()->find($created->getId());
        $this->assertNotNull($row);
        $this->assertSame('teste@example.com', $row->address);
        $this->assertSame((string) $department->id, (string) $row->department_id);
    }

    public function test_find_by_id_returns_entity(): void
    {
        $department = Department::query()->create(['department' => 'Teste']);
        $created = $this->repository->create(new EmailConfigEntity(
            id: null,
            email: 'buscar@example.com',
            departmentId: (string) $department->id
        ));

        $found = $this->repository->findById((string) $created->getId());

        $this->assertNotNull($found);
        $this->assertSame($created->getId(), $found->getId());
        $this->assertSame('buscar@example.com', $found->getEmail());
        $this->assertSame((string) $department->id, $found->getDepartmentId());
    }

    public function test_find_by_id_returns_null_when_missing(): void
    {
        $found = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($found);
    }

    public function test_update_updates_address_and_department(): void
    {
        $departmentA = Department::query()->create(['department' => 'A']);
        $departmentB = Department::query()->create(['department' => 'B']);

        $created = $this->repository->create(new EmailConfigEntity(
            id: null,
            email: 'antes@example.com',
            departmentId: (string) $departmentA->id
        ));

        $updated = $this->repository->update(new EmailConfigEntity(
            id: (string) $created->getId(),
            email: 'depois@example.com',
            departmentId: (string) $departmentB->id
        ));

        $this->assertNotNull($updated);
        $this->assertSame('depois@example.com', $updated->getEmail());
        $this->assertSame((string) $departmentB->id, $updated->getDepartmentId());

        $row = EmailSetting::query()->find($created->getId());
        $this->assertSame('depois@example.com', $row->address);
        $this->assertSame((string) $departmentB->id, (string) $row->department_id);
    }

    public function test_update_returns_null_when_id_empty_or_missing(): void
    {
        $department = Department::query()->create(['department' => 'Teste']);

        $this->assertNull($this->repository->update(new EmailConfigEntity(
            id: null,
            email: 'x@example.com',
            departmentId: (string) $department->id
        )));

        $this->assertNull($this->repository->update(new EmailConfigEntity(
            id: '00000000-0000-0000-0000-000000000000',
            email: 'x@example.com',
            departmentId: (string) $department->id
        )));
    }

    public function test_delete_by_id_deletes_record(): void
    {
        $department = Department::query()->create(['department' => 'Teste']);
        $created = $this->repository->create(new EmailConfigEntity(
            id: null,
            email: 'excluir@example.com',
            departmentId: (string) $department->id
        ));

        $this->assertTrue($this->repository->deleteById((string) $created->getId()));
        $this->assertNull(EmailSetting::query()->find($created->getId()));
    }

    public function test_delete_by_id_returns_false_when_missing_or_empty(): void
    {
        $this->assertFalse($this->repository->deleteById(''));
        $this->assertFalse($this->repository->deleteById('00000000-0000-0000-0000-000000000000'));
    }

    public function test_find_all_applies_filters_and_search(): void
    {
        $departmentA = Department::query()->create(['department' => 'Comercial']);
        $departmentB = Department::query()->create(['department' => 'Financeiro']);

        $this->repository->create(new EmailConfigEntity(
            id: null,
            email: 'a-comercial@example.com',
            departmentId: (string) $departmentA->id
        ));

        $this->repository->create(new EmailConfigEntity(
            id: null,
            email: 'b-fin@example.com',
            departmentId: (string) $departmentB->id
        ));

        $filteredByAddress = $this->repository->findAll([
            'address' => 'a-comercial',
            'sortField' => 'address',
            'sortDirection' => 'asc',
        ]);
        $this->assertCount(1, $filteredByAddress['data']);
        $this->assertSame('a-comercial@example.com', $filteredByAddress['data'][0]->getEmail());

        $filteredByDepartment = $this->repository->findAll([
            'departmentId' => (string) $departmentB->id,
            'sortField' => 'address',
            'sortDirection' => 'asc',
        ]);
        $this->assertCount(1, $filteredByDepartment['data']);
        $this->assertSame('b-fin@example.com', $filteredByDepartment['data'][0]->getEmail());

        $searchedByDepartmentName = $this->repository->findAll([
            'search' => 'Comercial',
            'sortField' => 'address',
            'sortDirection' => 'asc',
        ]);
        $this->assertCount(1, $searchedByDepartmentName['data']);
        $this->assertSame('a-comercial@example.com', $searchedByDepartmentName['data'][0]->getEmail());
    }
}

