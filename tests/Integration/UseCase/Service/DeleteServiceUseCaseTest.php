<?php

namespace Tests\Integration\UseCase\Service;

use App\Infrastructure\Eloquent\Service\EloquentServiceRepository;
use App\Models\ServiceUnity;
use App\UseCase\Service\CreateServiceUseCase;
use App\UseCase\Service\DTO\CreateServiceInputDto;
use App\UseCase\Service\DeleteServiceUseCase;
use App\UseCase\Service\GetServiceUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class DeleteServiceUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private DeleteServiceUseCase $useCase;
    private EloquentServiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentServiceRepository();
        $this->useCase = new DeleteServiceUseCase($this->repository);
    }

    public function test_throws_when_id_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->useCase->execute('');
    }

    public function test_deletes_service(): void
    {
        $unity = ServiceUnity::query()->create([
            'unity' => 'Hora',
        ]);

        $create = new CreateServiceUseCase($this->repository);
        $created = $create->execute(new CreateServiceInputDto(
            service: 'Excluir',
            unityId: (string) $unity->id,
            description: null
        ));

        $result = $this->useCase->execute($created->id);

        $this->assertTrue($result);

        $get = new GetServiceUseCase($this->repository);
        $this->assertNull($get->execute($created->id));
    }

    public function test_returns_false_when_not_found(): void
    {
        $result = $this->useCase->execute('00000000-0000-0000-0000-000000000000');
        $this->assertFalse($result);
    }
}
