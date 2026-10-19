<?php

namespace Tests\Integration\UseCase\Service;

use App\Infrastructure\Eloquent\Service\EloquentServiceRepository;
use App\Models\ServiceUnity;
use App\UseCase\Service\CreateServiceUseCase;
use App\UseCase\Service\DTO\CreateServiceInputDto;
use App\UseCase\Service\GetServiceUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class GetServiceUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private GetServiceUseCase $useCase;
    private EloquentServiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentServiceRepository();
        $this->useCase = new GetServiceUseCase($this->repository);
    }

    public function test_throws_when_id_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $result = $this->useCase->execute('00000000-0000-0000-0000-000000000000');
        $this->assertNull($result);
    }

    public function test_returns_service_when_found(): void
    {
        $unity = ServiceUnity::query()->create([
            'unity' => 'Hora',
        ]);

        $create = new CreateServiceUseCase($this->repository);
        $created = $create->execute(new CreateServiceInputDto(
            service: 'Suporte',
            unityId: (string) $unity->id,
            description: 'Desc'
        ));

        $output = $this->useCase->execute($created->id);

        $this->assertNotNull($output);
        $this->assertSame('Suporte', $output->service);
        $this->assertSame((string) $unity->id, $output->unityId);
        $this->assertSame('Hora', $output->unity);
    }
}
