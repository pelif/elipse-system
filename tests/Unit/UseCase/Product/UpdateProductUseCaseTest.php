<?php

namespace Tests\Unit\UseCase\Product;

use App\Domain\Product\Entities\Product as ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\DTO\UpdateProductInputDto;
use App\UseCase\Product\UpdateProductUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateProductUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $useCase = new UpdateProductUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_update_fails(): void
    {
        $input = new UpdateProductInputDto(
            id: '44444444-4444-4444-4444-444444444444',
            name: 'Novo',
            description: null
        );

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn(null);

        $useCase = new UpdateProductUseCase($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $input = new UpdateProductInputDto(
            id: '55555555-5555-5555-5555-555555555555',
            name: 'Atualizado',
            description: 'X'
        );

        $updated = new ProductEntity(
            id: $input->id,
            name: 'Atualizado',
            description: 'X',
            createdAt: null,
            updatedAt: null
        );

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn($updated);

        $useCase = new UpdateProductUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Atualizado', $output->name);
    }
}
