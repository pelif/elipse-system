<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Entities\ResourceCategory;
use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\CreateResourceCategoryUseCase;
use App\UseCase\ResourceCategory\DTO\CreateResourceCategoryInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateResourceCategoryUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $useCase = new CreateResourceCategoryUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_creates_resource_category(): void
    {
        $input = new CreateResourceCategoryInputDto(
            name: 'Nova categoria',
            description: 'Detalhes',
        );

        $createdEntity = new ResourceCategory(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            name: 'Nova categoria',
            description: 'Detalhes',
        );

        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input): bool {
                return $entity instanceof ResourceCategory
                    && $entity->getId() === null
                    && $entity->getName() === $input->name
                    && $entity->getDescription() === $input->description;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateResourceCategoryUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $output->id);
        $this->assertSame('Nova categoria', $output->name);
        $this->assertSame('Detalhes', $output->description);
    }
}
