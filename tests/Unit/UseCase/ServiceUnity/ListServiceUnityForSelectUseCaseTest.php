<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ServiceUnity;

use App\Domain\ServiceUnity\Repositories\ServiceUnityRepositoryInterface;
use App\UseCase\ServiceUnity\ListServiceUnityForSelectUseCase;
use Mockery;
use Tests\TestCase;

class ListServiceUnityForSelectUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_select_options_from_repository(): void
    {
        $options = [
            ['id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'label' => 'Diária'],
            ['id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'label' => 'Hora'],
        ];

        $repository = Mockery::mock(ServiceUnityRepositoryInterface::class);
        $repository
            ->shouldReceive('findAllForSelect')
            ->once()
            ->andReturn($options);

        $useCase = new ListServiceUnityForSelectUseCase($repository);
        $out = $useCase->execute();

        $this->assertSame($options, $out);
        $this->assertSame('Diária', $out[0]['label']);
        $this->assertSame('Hora', $out[1]['label']);
    }
}
