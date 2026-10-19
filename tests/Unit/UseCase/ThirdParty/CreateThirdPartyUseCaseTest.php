<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ThirdParty;

use App\Domain\Customer\ValueObjects\Document;
use App\Domain\ThirdParty\Entities\ThirdParty;
use App\Domain\ThirdParty\Repositories\ThirdPartyRepositoryInterface;
use App\UseCase\ThirdParty\CreateThirdPartyUseCase;
use App\UseCase\ThirdParty\DTO\CreateThirdPartyInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateThirdPartyUseCaseTest extends TestCase
{
    private const VALID_CNPJ = '11222333000181';

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $useCase = new CreateThirdPartyUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_throws_when_cnpj_is_invalid_before_repository(): void
    {
        $input = new CreateThirdPartyInputDto(
            name: 'Bad Doc',
            alias: null,
            phone: '1',
            phoneAlternative: null,
            email: 'e@test.local',
            emailAlternative: null,
            indication: null,
            observation: null,
            companyName: null,
            document: '11.111.111/1111-11',
            active: true,
        );

        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository->shouldNotReceive('create');

        $useCase = new CreateThirdPartyUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute($input);
    }

    public function test_creates_third_party(): void
    {
        $input = new CreateThirdPartyInputDto(
            name: 'Supplier',
            alias: null,
            phone: '11999990000',
            phoneAlternative: null,
            email: 's@test.local',
            emailAlternative: null,
            indication: null,
            observation: null,
            companyName: 'Razão',
            document: self::VALID_CNPJ,
            active: true,
            createdBy: 5,
            serviceIds: ['bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb'],
        );

        $createdEntity = new ThirdParty(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            name: $input->name,
            alias: null,
            phone: $input->phone,
            phoneAlternative: null,
            email: $input->email,
            emailAlternative: null,
            indication: null,
            observation: null,
            companyName: $input->companyName,
            document: Document::fromString(self::VALID_CNPJ),
            active: true,
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
            serviceIds: ['bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb'],
            services: [
                [
                    'id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
                    'service' => 'Som',
                ],
            ],
        );

        $repository = Mockery::mock(ThirdPartyRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input): bool {
                return $entity instanceof ThirdParty
                    && $entity->getId() === null
                    && $entity->getName() === $input->name
                    && $entity->getEmail() === $input->email
                    && self::VALID_CNPJ === $entity->getDocument()->getValue()
                    && $entity->getServiceIds() === $input->serviceIds;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateThirdPartyUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $output->id);
        $this->assertSame('Supplier', $output->name);
        $this->assertSame(self::VALID_CNPJ, $output->document);
        $this->assertSame(['bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb'], $output->serviceIds);
        $this->assertSame(0, $output->jobsCount);
        $this->assertNull($output->lastJobCode);
    }
}
