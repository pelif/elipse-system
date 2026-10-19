<?php

namespace Tests\Unit\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Entities\ServiceUnity;
use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\GetServiceUnity;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Mockery;
use Override;
use Tests\TestCase;

class GetServiceUnityTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_empty(): void
    {
        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $useCase = new GetServiceUnity($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $repository
            ->shouldReceive('findById')
            ->once()
            ->with('22222222222-222222222-2222222222-2222222222-22222222222')
            ->andReturn(null);

        $useCase = new GetServiceUnity($repository);
        $this->assertNull($useCase->execute('22222222222-222222222-2222222222-2222222222-22222222222'));

    }

    public function test_returns_output_when_found(): void
    {
        $entity = new ServiceUnity(
            id: '33333333333-333333333-333333333-333333333-333333',
            unity: 'Service Unity 001',
            createdBy: 4,
            updatedBy: 5,
            createdAt: new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')),
            updatedAt: null
        );

        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $repository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($entity);

        $useCase = new GetServiceUnity($repository);
        $output = $useCase->execute('33333333333-333333333-333333333-333333333-333333');

        $this->assertNotNull($output);
        $this->assertSame('Service Unity 001', $output->unity);
        $this->assertSame(4, $output->createdBy);
        $this->assertSame(5, $output->updatedBy);
    }


}
