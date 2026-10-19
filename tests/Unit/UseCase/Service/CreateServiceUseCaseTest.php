<?php

namespace Tests\Unit\UseCase\Service;

use App\Domain\Service\Entities\Service as ServiceEntity;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\CreateServiceUseCase;
use App\UseCase\Service\DTO\CreateServiceInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateServiceUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $useCase = new CreateServiceUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_creates_service(): void
    {
        $unityId = 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa';

        $input = new CreateServiceInputDto(
            service: 'Manutenção',
            unityId: $unityId,
            description: 'Descrição',
            createdBy: 1
        );

        $createdEntity = new ServiceEntity(
            id: '11111111-1111-1111-1111-111111111111',
            service: 'Manutenção',
            description: 'Descrição',
            unityId: $unityId,
            unity: 'Hora',
            createdBy: 1,
            updatedBy: null,
            createdAt: null,
            updatedAt: null
        );

        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input, $unityId) {
                return $entity instanceof ServiceEntity
                    && $entity->getId() === null
                    && $entity->getService() === $input->service
                    && $entity->getUnityId() === $unityId;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateServiceUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertSame('11111111-1111-1111-1111-111111111111', $output->id);
        $this->assertSame('Manutenção', $output->service);
        $this->assertSame($unityId, $output->unityId);
        $this->assertSame('Hora', $output->unity);
    }
}
