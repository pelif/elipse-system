<?php

namespace Tests\Unit\UseCase\Service;

use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\UseCase\Service\DeleteServiceUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class DeleteServiceUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $useCase = new DeleteServiceUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_repository_result(): void
    {
        $repository = Mockery::mock(ServiceRepositoryInterface::class);
        $repository
            ->shouldReceive('deleteById')
            ->once()
            ->with('66666666-6666-6666-6666-666666666666')
            ->andReturn(true);

        $useCase = new DeleteServiceUseCase($repository);
        $this->assertTrue($useCase->execute('66666666-6666-6666-6666-666666666666'));
    }
}
