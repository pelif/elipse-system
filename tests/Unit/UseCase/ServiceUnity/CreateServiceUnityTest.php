<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Entities\ServiceUnity;
use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\CreateServiceUnity;
use App\UseCase\ServiceUnity\DTO\ServiceUnityInputDto;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateServiceUnityTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_with_invalid_inputs(): void
    {
        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $useCase = new CreateServiceUnity($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_create_service_unity_with_output_returns(): void
    {
        $input = new ServiceUnityInputDto(
            id: null,
            unity: 'Service Unity Test 01',
            createdBy: 1,
            updatedBy: 10,
            createdAt: '2026-05-05 10:10:00',
            updatedAt: (new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s'),
        );

        $created = new ServiceUnity(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            unity: $input->unity,
            createdBy: $input->createdBy,
            updatedBy: $input->updatedBy,
            createdAt: new DateTimeImmutable($input->createdAt),
            updatedAt: new DateTimeImmutable($input->updatedAt)
        );

        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input): bool {
                return $entity instanceof ServiceUnity
                    && $entity->getId() === null
                    && $entity->getUnity() === $input->unity
                    && $entity->getCreator() === $input->createdBy
                    && $entity->getEditor() === $input->updatedBy
                    && $entity->getCreatedAt()?->format('Y-m-d H:i:s') === $input->createdAt
                    && $entity->getUpdatedAt()?->format('Y-m-d H:i:s') === $input->updatedAt;
            }))
            ->andReturn($created);

        $useCase = new CreateServiceUnity($repository);
        $output = $useCase->execute($input);

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $output->id);
        $this->assertSame('Service Unity Test 01', $output->unity);
        $this->assertSame(1, $output->createdBy);
        $this->assertSame(10, $output->updatedBy);
        $this->assertSame($input->createdAt, $output->createdAt);
        $this->assertSame($input->updatedAt, $output->updatedAt);

    }
}
