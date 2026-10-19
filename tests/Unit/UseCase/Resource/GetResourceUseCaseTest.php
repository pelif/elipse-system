<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Resource;

use App\Domain\Resource\Entities\Resource;
use App\Domain\Resource\Repositories\ResourceRepositoryInterface;
use App\UseCase\Resource\GetResourceUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class GetResourceUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_empty(): void
    {
        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $useCase = new GetResourceUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee')->andReturn(null);

        $useCase = new GetResourceUseCase($repository);
        $this->assertNull($useCase->execute('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee'));
    }

    public function test_returns_output_when_found(): void
    {
        $entity = new Resource(
            id: 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            categoryId: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            departmentId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            resourceName: 'Mic',
            patrimonialCode: 'M1',
            manufacturer: null,
            model: null,
            serialNumber: null,
            responsibleId: null,
            notes: null,
            documentationLink: null,
            createdAt: null,
            createdBy: 3,
            updatedAt: null,
            updatedBy: null,
        );

        $repository = Mockery::mock(ResourceRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($entity);

        $useCase = new GetResourceUseCase($repository);
        $output = $useCase->execute('ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertNotNull($output);
        $this->assertSame('Mic', $output->resourceName);
        $this->assertSame(3, $output->createdBy);
    }
}
