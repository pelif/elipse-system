<?php

namespace Tests\Unit\UseCase\Status;

use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\Status\DTO\UpdateJobStatusInputDto;
use App\UseCase\Status\UpdateJobStatusUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateJobStatusUseCaseTest extends TestCase
{
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(StatusRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $useCase = new UpdateJobStatusUseCase($this->repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute([]);
    }

    public function test_returns_null_when_not_found(): void
    {
        $input = new UpdateJobStatusInputDto(
            id: 'missing-id',
            status: 'Novo',
            observacoes: null
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('missing-id')
            ->andReturn(null);

        $useCase = new UpdateJobStatusUseCase($this->repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_null_when_type_is_not_job(): void
    {
        $input = new UpdateJobStatusInputDto(
            id: 's1',
            status: 'Novo',
            observacoes: null
        );

        $entity = new StatusEntity(
            id: 's1',
            status: 'Antigo',
            type: StatusType::ORDER,
            observacoes: null
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('s1')
            ->andReturn($entity);

        $useCase = new UpdateJobStatusUseCase($this->repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_updates_and_returns_output(): void
    {
        $input = new UpdateJobStatusInputDto(
            id: 'uuid-1',
            status: 'Atualizado',
            observacoes: 'Obs'
        );

        $existing = new StatusEntity(
            id: 'uuid-1',
            status: 'Antigo',
            type: StatusType::JOB,
            observacoes: null
        );

        $saved = new StatusEntity(
            id: 'uuid-1',
            status: 'Atualizado',
            type: StatusType::JOB,
            observacoes: 'Obs'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('uuid-1')
            ->andReturn($existing);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($e) use ($input) {
                return $e instanceof StatusEntity
                    && $e->getId() === $input->id
                    && $e->getStatus() === $input->status
                    && $e->getType() === StatusType::JOB
                    && $e->getObservacoes() === $input->observacoes;
            }))
            ->andReturn($saved);

        $useCase = new UpdateJobStatusUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('uuid-1', $output->id);
        $this->assertSame('Atualizado', $output->status);
        $this->assertSame(StatusType::JOB->value, $output->type);
        $this->assertSame('Obs', $output->observacoes);
    }
}
