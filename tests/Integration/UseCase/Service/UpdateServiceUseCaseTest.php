<?php

namespace Tests\Integration\UseCase\Service;

use App\Infrastructure\Eloquent\Service\EloquentServiceRepository;
use App\Models\Service as ServiceModel;
use App\Models\ServiceUnity;
use App\UseCase\Service\CreateServiceUseCase;
use App\UseCase\Service\DTO\CreateServiceInputDto;
use App\UseCase\Service\DTO\UpdateServiceInputDto;
use App\UseCase\Service\UpdateServiceUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class UpdateServiceUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private UpdateServiceUseCase $useCase;
    private EloquentServiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentServiceRepository();
        $this->useCase = new UpdateServiceUseCase($this->repository);
    }

    public function test_throws_when_input_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->useCase->execute(null);
    }

    public function test_returns_null_when_not_found(): void
    {
        $input = new UpdateServiceInputDto(
            id: '00000000-0000-0000-0000-000000000000',
            service: 'Não Existe',
            unityId: '11111111-1111-1111-1111-111111111111',
            description: null
        );

        $result = $this->useCase->execute($input);
        $this->assertNull($result);
    }

    public function test_updates_service(): void
    {
        $unityHora = ServiceUnity::query()->create([
            'unity' => 'Hora',
        ]);
        $unityDiaria = ServiceUnity::query()->create([
            'unity' => 'Diária',
        ]);

        $create = new CreateServiceUseCase($this->repository);
        $created = $create->execute(new CreateServiceInputDto(
            service: 'Original',
            unityId: (string) $unityHora->id,
            description: 'A'
        ));

        $input = new UpdateServiceInputDto(
            id: $created->id,
            service: 'Atualizado',
            unityId: (string) $unityDiaria->id,
            description: 'B',
            updatedBy: null
        );

        $output = $this->useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Atualizado', $output->service);
        $this->assertSame('B', $output->description);
        $this->assertSame((string) $unityDiaria->id, $output->unityId);
        $this->assertSame('Diária', $output->unity);

        $fromDb = ServiceModel::query()->find($created->id);
        $this->assertSame((string) $unityDiaria->id, $fromDb->unity_id);
    }
}
