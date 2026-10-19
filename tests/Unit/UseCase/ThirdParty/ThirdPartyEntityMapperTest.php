<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\ThirdParty;

use App\UseCase\ThirdParty\DTO\CreateThirdPartyInputDto;
use App\UseCase\ThirdParty\DTO\UpdateThirdPartyInputDto;
use App\UseCase\ThirdParty\ThirdPartyEntityMapper;
use InvalidArgumentException;
use Tests\TestCase;

class ThirdPartyEntityMapperTest extends TestCase
{
    private const VALID_CNPJ = '11222333000181';

    public function test_from_create_dto_builds_entity_with_normalized_document(): void
    {
        $input = new CreateThirdPartyInputDto(
            name: 'Mapped Co',
            alias: null,
            phone: '11 99999-8888',
            phoneAlternative: null,
            email: 'm@test.local',
            emailAlternative: null,
            indication: 'Ind',
            observation: null,
            companyName: 'Razão',
            document: '11.222.333/0001-81',
            active: true,
            serviceIds: ['cccccccc-cccc-cccc-cccc-cccccccccccc'],
        );

        $entity = ThirdPartyEntityMapper::fromCreateDto($input);

        $this->assertNull($entity->getId());
        $this->assertSame('Mapped Co', $entity->getName());
        $this->assertSame(self::VALID_CNPJ, $entity->getDocument()->getValue());
        $this->assertSame('Ind', $entity->getIndication());
        $this->assertSame(['cccccccc-cccc-cccc-cccc-cccccccccccc'], $entity->getServiceIds());
    }

    public function test_from_create_dto_throws_for_invalid_cnpj(): void
    {
        $input = new CreateThirdPartyInputDto(
            name: 'X',
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

        $this->expectException(InvalidArgumentException::class);
        ThirdPartyEntityMapper::fromCreateDto($input);
    }

    public function test_from_update_dto_throws_for_invalid_cnpj(): void
    {
        $input = new UpdateThirdPartyInputDto(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'X',
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

        $this->expectException(InvalidArgumentException::class);
        ThirdPartyEntityMapper::fromUpdateDto($input);
    }
}
