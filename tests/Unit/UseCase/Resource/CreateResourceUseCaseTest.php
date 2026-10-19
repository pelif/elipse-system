<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Resource;

use App\Domain\Resource\Entities\Resource;
use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\CreateResourceUseCase;
use App\UseCase\Resource\DTO\CreateResourceInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateResourceUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $useCase = new CreateResourceUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_creates_resource_and_returns_output(): void
    {
        $input = new CreateResourceInputDto(
            categoryId: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            departmentId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            resourceName: 'Scanner',
            patrimonialCode: 'PAT-S',
            createdBy: 77,
            updatedBy: null,
        );

        $createdEntity = new Resource(
            id: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            categoryId: $input->categoryId,
            departmentId: $input->departmentId,
            resourceName: $input->resourceName,
            patrimonialCode: $input->patrimonialCode,
            manufacturer: null,
            model: null,
            serialNumber: null,
            responsibleId: null,
            notes: null,
            documentationLink: null,
            createdAt: '2026-01-01T00:00:00+00:00',
            createdBy: 77,
            updatedAt: null,
            updatedBy: null,
        );

        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input): bool {
                return $entity instanceof Resource
                    && $entity->getId() === null
                    && $entity->getCategoryId() === $input->categoryId
                    && $entity->getDepartmentId() === $input->departmentId
                    && $entity->getResourceName() === $input->resourceName
                    && $entity->getPatrimonialCode() === $input->patrimonialCode
                    && $entity->getCreatedBy() === 77
                    && $entity->getUpdatedBy() === null;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateResourceUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertSame('dddddddd-dddd-dddd-dddd-dddddddddddd', $output->id);
        $this->assertSame('Scanner', $output->resourceName);
        $this->assertSame(77, $output->createdBy);
    }
}
