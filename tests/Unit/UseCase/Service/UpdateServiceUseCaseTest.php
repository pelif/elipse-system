<?php

namespace Tests\Unit\UseCase\Service;

use App\Domain\Service\Entities\Service as ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\DTO\UpdateServiceInputDto;
use App\UseCase\Service\UpdateServiceUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateServiceUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $useCase = new UpdateServiceUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_update_fails(): void
    {
        $unityId = 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa';

        $input = new UpdateServiceInputDto(
            id: '44444444-4444-4444-4444-444444444444',
            service: 'Suporte',
            unityId: $unityId,
            description: null,
            updatedBy: 1
        );

        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn(null);

        $useCase = new UpdateServiceUseCase($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $unityId = 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb';

        $input = new UpdateServiceInputDto(
            id: '55555555-5555-5555-5555-555555555555',
            service: 'Treinamento',
            unityId: $unityId,
            description: 'Novo',
            updatedBy: 2
        );

        $updated = new ServiceEntity(
            id: $input->id,
            service: 'Treinamento',
            description: 'Novo',
            unityId: $unityId,
            unity: 'Diária',
            createdBy: null,
            updatedBy: 2,
            createdAt: null,
            updatedAt: null
        );

        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $repository
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input, $unityId) {
                return $entity instanceof ServiceEntity
                    && $entity->getUnityId() === $unityId
                    && $entity->getUnityId() !== null
                    && $entity->getService() === $input->service;
            }))
            ->andReturn($updated);

        $useCase = new UpdateServiceUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Treinamento', $output->service);
        $this->assertSame($unityId, $output->unityId);
        $this->assertSame('Diária', $output->unity);
    }
}
