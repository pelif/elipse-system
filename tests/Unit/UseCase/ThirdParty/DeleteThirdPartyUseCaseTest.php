<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ThirdParty;

use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\DeleteThirdPartyUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class DeleteThirdPartyUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $useCase = new DeleteThirdPartyUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_repository_result(): void
    {
        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldReceive('deleteById')->once()->with('99999999-9999-9999-9999-999999999999')->andReturn(true);

        $useCase = new DeleteThirdPartyUseCase($repository);
        $this->assertTrue($useCase->execute('99999999-9999-9999-9999-999999999999'));
    }
}
