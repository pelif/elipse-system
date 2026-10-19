<?php

namespace Tests\Unit\UseCase\Status;

use App\Domain\Status\Entities\Status as StatusEntity;
use App\Domain\Status\Enums\StatusType;
use App\Domain\Status\Repositories\StatusRepositoryInterface;
use App\UseCase\Status\GetJobStatusUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class GetJobStatusUseCaseTest extends TestCase
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
        $useCase = new GetJobStatusUseCase($this->repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_not_found(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('x')
            ->andReturn(null);

        $useCase = new GetJobStatusUseCase($this->repository);
        $output = $useCase->execute('x');

        $this->assertNull($output);
    }

    public function test_returns_null_when_type_is_not_job(): void
    {
        $entity = new StatusEntity(
            id: 's1',
            status: 'Qualquer',
            type: StatusType::ORDER,
            observacoes: null
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('s1')
            ->andReturn($entity);

        $useCase = new GetJobStatusUseCase($this->repository);
        $output = $useCase->execute('s1');

        $this->assertNull($output);
    }

    public function test_returns_output_when_found_and_type_job(): void
    {
        $entity = new StatusEntity(
            id: 's2',
            status: 'Em andamento',
            type: StatusType::JOB,
            observacoes: 'Obs'
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with('s2')
            ->andReturn($entity);

        $useCase = new GetJobStatusUseCase($this->repository);
        $output = $useCase->execute('s2');

        $this->assertNotNull($output);
        $this->assertSame('s2', $output->id);
        $this->assertSame('Em andamento', $output->status);
        $this->assertSame(StatusType::JOB->value, $output->type);
        $this->assertSame('Obs', $output->observacoes);
    }
}

