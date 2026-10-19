<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ResourceCategory;

use App\Domain\ResourceCategory\Entities\ResourceCategory;
use App\Domain\ResourceCategory\Repositories\ResourceCategoryRepositoryInterface;
use App\UseCase\ResourceCategory\DTO\UpdateResourceCategoryInputDto;
use App\UseCase\ResourceCategory\UpdateResourceCategoryUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateResourceCategoryUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $useCase = new UpdateResourceCategoryUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_update_fails(): void
    {
        $input = new UpdateResourceCategoryInputDto(
            id: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            name: 'Novo nome',
            description: null,
        );

        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn(null);

        $useCase = new UpdateResourceCategoryUseCase($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $input = new UpdateResourceCategoryInputDto(
            id: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            name: 'Atualizada',
            description: 'Nova descrição',
        );

        $updated = new ResourceCategory(
            id: $input->id,
            name: $input->name,
            description: $input->description,
        );

        $repository = Mockery::mock(ResourceCategoryRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn($updated);

        $useCase = new UpdateResourceCategoryUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Atualizada', $output->name);
        $this->assertSame('Nova descrição', $output->description);
    }
}
