<?php

namespace Tests\Unit\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Entities\ServiceUnity;
use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\DTO\ServiceUnityInputDto;
use App\UseCase\ServiceUnity\UpdateServiceUnity;
use DateTimeImmutable;
use InvalidArgumentException;
use Mockery;
use Override;
use Tests\TestCase;

class UpdateServiceUnityTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_with_invalid_inputs(): void
    {
        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $useCase = new UpdateServiceUnity($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_fails(): void
    {
        $input = new ServiceUnityInputDto(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            unity: 'Service Unity Test 01',
            createdBy: 1,
            updatedBy: 10,
            createdAt: '2026-05-05 10:10:00',
            updatedAt: '2026-05-06 10:10:00'
        );

        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);

        $repository
            ->shouldReceive('update')
            ->once()
            ->andReturn(null);

        $useCase = new UpdateServiceUnity($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $input = new ServiceUnityInputDto(
            id: '111111111111-1111111111-111111111-1111111111',
            unity: 'Service Unity 001',
            createdBy: 5,
            updatedBy: 6,
            createdAt: '2020-10-10 10:00:00',
            updatedAt: null
        );

        $updated = new ServiceUnity(
            id: $input->id,
            unity: 'Atualizacao Service Unity 001',
            createdBy: 4,
            updatedBy: 5,
            createdAt: new DateTimeImmutable('2020-10-10 10:00:00'),
            updatedAt: new DateTimeImmutable('2020-10-20 11:00:00')
        );

        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $repository
            ->shouldReceive('update')
            ->once()
            ->andReturn($updated);

        $useCase = new UpdateServiceUnity($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('111111111111-1111111111-111111111-1111111111', $output->id);
        $this->assertSame('Atualizacao Service Unity 001', $output->unity);
        $this->assertSame(4, $output->createdBy);
        $this->assertSame(5, $output->updatedBy);
        $this->assertSame('2020-10-10 10:00:00', $output->createdAt);
        $this->assertSame('2020-10-20 11:00:00', $output->updatedAt);
    }


}
