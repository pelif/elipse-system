<?php

declare(strict_types=1);

namespace Tests\Integration\UseCase\Resource;

use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\Models\Department\Department;
use App\Models\ResourceCategory;
use App\Models\User;
use App\UseCase\Resource\CreateResourceUseCase;
use App\UseCase\Resource\DeleteResourceUseCase;
use App\UseCase\Resource\DTO\CreateResourceInputDto;
use App\UseCase\Resource\DTO\UpdateResourceInputDto;
use App\UseCase\Resource\GetResourceUseCase;
use App\UseCase\Resource\ListResourcesUseCase;
use App\UseCase\Resource\UpdateResourceUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResourceUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private ResourceRepositoryInterface $repository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ResourceRepositoryInterface::class);
        $this->user = User::factory()->create();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function seedCategoryAndDepartment(): array
    {
        $categoryId = (string) Str::uuid();
        ResourceCategory::forceCreate([
            'id' => $categoryId,
            'name' => 'Cat UC',
            'description' => null,
        ]);

        $departmentId = (string) Str::uuid();
        Department::forceCreate([
            'id' => $departmentId,
            'department' => 'Dept UC',
        ]);

        return [$categoryId, $departmentId];
    }

    public function test_create_use_case_persists_and_returns_audit_ids(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        $input = new CreateResourceInputDto(
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: 'Microfone',
            patrimonialCode: 'UC-PAT-1',
            createdBy: (int) $this->user->id,
            updatedBy: null,
        );

        $output = (new CreateResourceUseCase($this->repository))->execute($input);

        $this->assertNotEmpty($output->id);
        $this->assertSame('Microfone', $output->resourceName);
        $this->assertSame((int) $this->user->id, $output->createdBy);
        $this->assertNull($output->updatedBy);
    }

    public function test_create_use_case_throws_for_invalid_input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CreateResourceUseCase($this->repository))->execute('invalid');
    }

    public function test_get_use_case_returns_output_for_existing_id(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        $created = (new CreateResourceUseCase($this->repository))->execute(new CreateResourceInputDto(
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: 'Item Get',
            patrimonialCode: 'G-1',
            createdBy: (int) $this->user->id,
        ));

        $output = (new GetResourceUseCase($this->repository))->execute($created->id);

        $this->assertNotNull($output);
        $this->assertSame($created->id, $output->id);
        $this->assertSame('Item Get', $output->resourceName);
    }

    public function test_get_use_case_returns_null_for_missing_id(): void
    {
        $output = (new GetResourceUseCase($this->repository))->execute('00000000-0000-0000-0000-000000000000');
        $this->assertNull($output);
    }

    public function test_update_use_case_persists_changes_and_updated_by(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        $created = (new CreateResourceUseCase($this->repository))->execute(new CreateResourceInputDto(
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: 'Antes',
            patrimonialCode: 'U-1',
            createdBy: (int) $this->user->id,
        ));

        $editor = User::factory()->create();

        $updateInput = new UpdateResourceInputDto(
            id: $created->id,
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: 'Depois',
            patrimonialCode: 'U-2',
            createdBy: $created->createdBy,
            updatedBy: (int) $editor->id,
        );

        $output = (new UpdateResourceUseCase($this->repository))->execute($updateInput);

        $this->assertNotNull($output);
        $this->assertSame('Depois', $output->resourceName);
        $this->assertSame('U-2', $output->patrimonialCode);
        $this->assertSame((int) $editor->id, $output->updatedBy);
        $this->assertSame($created->createdBy, $output->createdBy);
    }

    public function test_delete_use_case_removes_record(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        $created = (new CreateResourceUseCase($this->repository))->execute(new CreateResourceInputDto(
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: 'Remover',
            patrimonialCode: 'D-1',
            createdBy: (int) $this->user->id,
        ));

        $result = (new DeleteResourceUseCase($this->repository))->execute($created->id);

        $this->assertTrue($result);
        $this->assertNull((new GetResourceUseCase($this->repository))->execute($created->id));
    }

    public function test_list_use_case_returns_paginated_data(): void
    {
        [$categoryId, $departmentId] = $this->seedCategoryAndDepartment();

        (new CreateResourceUseCase($this->repository))->execute(new CreateResourceInputDto(
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: 'Lista A',
            patrimonialCode: 'L-A',
            createdBy: (int) $this->user->id,
        ));

        (new CreateResourceUseCase($this->repository))->execute(new CreateResourceInputDto(
            categoryId: $categoryId,
            departmentId: $departmentId,
            resourceName: 'Lista B',
            patrimonialCode: 'L-B',
            createdBy: (int) $this->user->id,
        ));

        $result = (new ListResourcesUseCase($this->repository))->execute([]);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('filters', $result);
        $this->assertCount(2, $result['data']);
        $this->assertSame(10, $result['meta']['per_page']);
        $this->assertSame(2, $result['meta']['total']);
    }
}
