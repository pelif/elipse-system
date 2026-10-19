<?php

namespace Tests\Unit\UseCase\Product;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\DeleteProductUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class DeleteProductUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $useCase = new DeleteProductUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_repository_result(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('deleteById')->once()->with('66666666-6666-6666-6666-666666666666')->andReturn(true);

        $useCase = new DeleteProductUseCase($repository);
        $this->assertTrue($useCase->execute('66666666-6666-6666-6666-666666666666'));
    }
}
