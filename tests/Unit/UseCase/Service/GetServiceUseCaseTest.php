<?php

namespace Tests\Unit\UseCase\Service;

use App\Domain\Service\Entities\Service as ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\GetServiceUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class GetServiceUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_empty(): void
    {
        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $useCase = new GetServiceUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $repository
            ->shouldReceive('findById')
            ->once()
            ->with('22222222-2222-2222-2222-222222222222')
            ->andReturn(null);

        $useCase = new GetServiceUseCase($repository);
        $this->assertNull($useCase->execute('22222222-2222-2222-2222-222222222222'));
    }

    public function test_returns_output_when_found(): void
    {
        $entity = new ServiceEntity(
            id: '33333333-3333-3333-3333-333333333333',
            service: 'Consultoria',
            description: 'Desc',
            unityId: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            unity: 'Hora',
            createdBy: null,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        );

        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($entity);

        $useCase = new GetServiceUseCase($repository);
        $output = $useCase->execute('33333333-3333-3333-3333-333333333333');

        $this->assertNotNull($output);
        $this->assertSame('Consultoria', $output->service);
        $this->assertSame('cccccccc-cccc-cccc-cccc-cccccccccccc', $output->unityId);
        $this->assertSame('Hora', $output->unity);
    }
}
