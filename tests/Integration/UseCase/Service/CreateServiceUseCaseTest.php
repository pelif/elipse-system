<?php

namespace Tests\Integration\UseCase\Service;

use App\Infrastructure\Eloquent\Service\EloquentServiceRepository;
use App\Models\ServiceUnity;
use App\UseCase\Service\CreateServiceUseCase;
use App\UseCase\Service\DTO\CreateServiceInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CreateServiceUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private CreateServiceUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new CreateServiceUseCase(new EloquentServiceRepository());
    }

    public function test_throws_when_input_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->useCase->execute(null);
    }

    public function test_creates_service_and_returns_output(): void
    {
        $unity = ServiceUnity::query()->create([
            'unity' => 'Hora',
        ]);

        $input = new CreateServiceInputDto(
            service: 'Manutenção',
            unityId: (string) $unity->id,
            description: 'Descrição',
            createdBy: null
        );

        $output = $this->useCase->execute($input);

        $this->assertNotNull($output->id);
        $this->assertSame('Manutenção', $output->service);
        $this->assertSame('Descrição', $output->description);
        $this->assertSame((string) $unity->id, $output->unityId);
        $this->assertSame('Hora', $output->unity);
    }
}
