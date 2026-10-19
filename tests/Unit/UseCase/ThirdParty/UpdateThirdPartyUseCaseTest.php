<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ThirdParty;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\Entities\ThirdParty;
use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\DTO\UpdateThirdPartyInputDto;
use App\UseCase\ThirdParty\UpdateThirdPartyUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateThirdPartyUseCaseTest extends TestCase
{
    private const VALID_CNPJ = '00000000000191';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $useCase = new UpdateThirdPartyUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_throws_when_cnpj_is_invalid_before_repository(): void
    {
        $input = new UpdateThirdPartyInputDto(
            id: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            name: 'X',
            alias: null,
            phone: '1',
            phoneAlternative: null,
            email: 'e@test.local',
            emailAlternative: null,
            indication: null,
            observation: null,
            companyName: null,
            document: '11222333000182',
            active: true,
        );

        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldNotReceive('update');

        $useCase = new UpdateThirdPartyUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute($input);
    }

    public function test_returns_null_when_update_fails(): void
    {
        $input = new UpdateThirdPartyInputDto(
            id: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            name: 'Novo',
            alias: null,
            phone: '1',
            phoneAlternative: null,
            email: 'e@test.local',
            emailAlternative: null,
            indication: null,
            observation: null,
            companyName: null,
            document: self::VALID_CNPJ,
            active: true,
        );

        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn(null);

        $useCase = new UpdateThirdPartyUseCase($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $input = new UpdateThirdPartyInputDto(
            id: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            name: 'Atualizado',
            alias: null,
            phone: '1144445555',
            phoneAlternative: null,
            email: 'e@test.local',
            emailAlternative: null,
            indication: 'Ind',
            observation: null,
            companyName: 'Co',
            document: self::VALID_CNPJ,
            active: false,
            serviceIds: ['eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee'],
        );

        $updated = new ThirdParty(
            id: $input->id,
            name: $input->name,
            alias: null,
            phone: $input->phone,
            phoneAlternative: null,
            email: $input->email,
            emailAlternative: null,
            indication: $input->indication,
            observation: null,
            companyName: $input->companyName,
            document: Document::fromString(self::VALID_CNPJ),
            active: false,
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
            serviceIds: ['eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee'],
        );

        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn($updated);

        $useCase = new UpdateThirdPartyUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Atualizado', $output->name);
        $this->assertFalse($output->active);
        $this->assertSame('Ind', $output->indication);
        $this->assertSame(['eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee'], $output->serviceIds);
    }
}
