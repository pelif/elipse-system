<?php

namespace Tests\Unit\UseCase\Product;

use App\Domain\Product\Entities\Product as ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\UseCase\Product\CreateProductUseCase;
use App\UseCase\Product\DTO\CreateProductInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateProductUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $useCase = new CreateProductUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_creates_product(): void
    {
        $input = new CreateProductInputDto(
            name: 'Caneta',
            description: null
        );

        $createdEntity = new ProductEntity(
            id: '11111111-1111-1111-1111-111111111111',
            name: 'Caneta',
            description: null,
            createdAt: null,
            updatedAt: null
        );

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input) {
                return $entity instanceof ProductEntity
                    && $entity->getId() === null
                    && $entity->getName() === $input->name;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateProductUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertSame('11111111-1111-1111-1111-111111111111', $output->id);
        $this->assertSame('Caneta', $output->name);
    }
}
