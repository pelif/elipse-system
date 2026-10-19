<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Resource;

use App\Domain\Resource\Entities\Resource;
use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\DTO\UpdateResourceInputDto;
use App\UseCase\Resource\UpdateResourceUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateResourceUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $useCase = new UpdateResourceUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_update_fails(): void
    {
        $input = new UpdateResourceInputDto(
            id: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            categoryId: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            departmentId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            resourceName: 'X',
            patrimonialCode: 'P',
            updatedBy: 10,
        );

        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn(null);

        $useCase = new UpdateResourceUseCase($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $input = new UpdateResourceInputDto(
            id: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            categoryId: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            departmentId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            resourceName: 'Nome novo',
            patrimonialCode: 'PAT-N',
            createdBy: 1,
            updatedBy: 55,
        );

        $updated = new Resource(
            id: $input->id,
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
            createdAt: null,
            createdBy: 1,
            updatedAt: '2026-02-02T00:00:00+00:00',
            updatedBy: 55,
        );

        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $repository
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($entity): bool {
                return $entity instanceof Resource
                    && $entity->getUpdatedBy() === 55;
            }))
            ->andReturn($updated);

        $useCase = new UpdateResourceUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Nome novo', $output->resourceName);
        $this->assertSame(55, $output->updatedBy);
    }
}
