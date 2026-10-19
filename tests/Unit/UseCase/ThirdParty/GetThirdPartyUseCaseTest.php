<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ThirdParty;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\Entities\ThirdParty;
use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\GetThirdPartyUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class GetThirdPartyUseCaseTest extends TestCase
{
    private const VALID_CNPJ = '60701190000104';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_empty(): void
    {
        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $useCase = new GetThirdPartyUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee')->andReturn(null);

        $useCase = new GetThirdPartyUseCase($repository);
        $this->assertNull($useCase->execute('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee'));
    }

    public function test_returns_output_when_found(): void
    {
        $entity = new ThirdParty(
            id: 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            name: 'Encontrado',
            alias: 'E',
            phone: '1',
            phoneAlternative: null,
            email: 'f@test.local',
            emailAlternative: null,
            indication: null,
            observation: null,
            companyName: 'Co',
            document: Document::fromString(self::VALID_CNPJ),
            active: true,
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($entity);

        $useCase = new GetThirdPartyUseCase($repository);
        $output = $useCase->execute('ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertNotNull($output);
        $this->assertSame('Encontrado', $output->name);
        $this->assertSame(self::VALID_CNPJ, $output->document);
        $this->assertSame([], $output->serviceIds);
        $this->assertSame(0, $output->jobsCount);
        $this->assertNull($output->lastJobCode);
    }
}
