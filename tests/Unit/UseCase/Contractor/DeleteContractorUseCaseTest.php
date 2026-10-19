<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Contractor;

use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\Contractor\DeleteContractorUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class DeleteContractorUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ContractorRepositoryInterface::class);
        $useCase = new DeleteContractorUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_repository_result(): void
    {
        $repository = Mockery::mock(ContractorRepositoryInterface::class);
        $repository->shouldReceive('deleteById')->once()->with('99999999-9999-9999-9999-999999999999')->andReturn(true);

        $useCase = new DeleteContractorUseCase($repository);
        $this->assertTrue($useCase->execute('99999999-9999-9999-9999-999999999999'));
    }
}
