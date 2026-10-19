<?php

namespace Tests\Unit\UseCase\Status;

use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\Status\CreateJobStatusUseCase;
use App\UseCase\Status\DTO\CreateJobStatusInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateJobStatusUseCaseTest extends TestCase
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
        $useCase = new CreateJobStatusUseCase($this->repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(['status' => 'X']);
    }

    public function test_creates_job_status_with_type_job(): void
    {
        $input = new CreateJobStatusInputDto(
            status: 'Aguardando',
            observacoes: 'Fila'
        );

        $createdEntity = new StatusEntity(
            id: 'uuid-status-1',
            status: $input->status,
            type: StatusType::JOB,
            observacoes: $input->observacoes
        );

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input) {
                if (!$entity instanceof StatusEntity) {
                    return false;
                }

                return $entity->getId() === null
                    && $entity->getStatus() === $input->status
                    && $entity->getType() === StatusType::JOB
                    && $entity->getObservacoes() === $input->observacoes;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateJobStatusUseCase($this->repository);
        $output = $useCase->execute($input);

        $this->assertSame('uuid-status-1', $output->id);
        $this->assertSame('Aguardando', $output->status);
        $this->assertSame(StatusType::JOB->value, $output->type);
        $this->assertSame('Fila', $output->observacoes);
    }
}

