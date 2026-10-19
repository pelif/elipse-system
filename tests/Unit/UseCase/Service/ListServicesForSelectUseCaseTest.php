<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Service;

use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\ListServicesForSelectUseCase;
use Mockery;
use Tests\TestCase;

class ListServicesForSelectUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_select_options_from_repository(): void
    {
        $options = [
            ['id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'label' => 'Áudio'],
            ['id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'label' => 'Iluminação'],
        ];

        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $repository
            ->shouldReceive('findAllForSelect')
            ->once()
            ->andReturn($options);

        $useCase = new ListServicesForSelectUseCase($repository);
        $out = $useCase->execute();

        $this->assertSame($options, $out);
        $this->assertSame('Áudio', $out[0]['label']);
    }
}
