<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\UseCase\Customer\DeleteCustomerUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class DeleteCustomerUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $useCase = new DeleteCustomerUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_repository_result(): void
    {
        $repository = Mockery::mock(CustomerRepositoryInterface::class);
        $repository->shouldReceive('deleteById')->once()->with('66666666-6666-6666-6666-666666666666')->andReturn(true);

        $useCase = new DeleteCustomerUseCase($repository);
        $this->assertTrue($useCase->execute('66666666-6666-6666-6666-666666666666'));
    }
}
