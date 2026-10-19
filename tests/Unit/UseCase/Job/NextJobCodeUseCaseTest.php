<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Job;

use App\Domain\Job\Repositories\JobRepositoryInterface;
use App\UseCase\Job\NextJobCodeUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class NextJobCodeUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_customer_id_is_missing(): void
    {
        $repository = Mockery::mock(JobRepositoryInterface::class);
        $repository->shouldReceive('nextCodeForCustomer')->never();

        $this->expectException(InvalidArgumentException::class);

        (new NextJobCodeUseCase($repository))->execute('   ');
    }

    public function test_returns_the_code_from_the_repository(): void
    {
        $repository = Mockery::mock(JobRepositoryInterface::class);
        $repository
            ->shouldReceive('nextCodeForCustomer')
            ->once()
            ->with('22222222-2222-2222-2222-222222222222')
            ->andReturn('TST1C_0004');

        $code = (new NextJobCodeUseCase($repository))->execute('  22222222-2222-2222-2222-222222222222  ');

        $this->assertSame('TST1C_0004', $code);
    }
}
