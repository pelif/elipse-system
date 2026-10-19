<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Contractor;

use App\UseCase\Contractor\ContractorEntityMapper;
use App\UseCase\Contractor\DTO\CreateContractorInputDto;
use App\UseCase\Contractor\DTO\UpdateContractorInputDto;
use Tests\TestCase;

class ContractorEntityMapperTest extends TestCase
{
    public function test_from_create_dto_builds_entity(): void
    {
        $input = new CreateContractorInputDto(
            name: 'Mapped Name',
            phone: '11 98888-7777',
            email: 'map@test.local',
            customerId: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            communicationCare: 'Care',
            createdBy: 3,
            updatedBy: 4,
        );

        $entity = ContractorEntityMapper::fromCreateDto($input);

        $this->assertNull($entity->getId());
        $this->assertSame('Mapped Name', $entity->getName());
        $this->assertSame('11 98888-7777', $entity->getPhone());
        $this->assertSame('map@test.local', $entity->getEmail());
        $this->assertSame('cccccccc-cccc-cccc-cccc-cccccccccccc', $entity->getCustomerId());
        $this->assertSame('Care', $entity->getCommunicationCare());
        $this->assertNull($entity->getTypeContact());
        $this->assertNull($entity->getCustomerName());
        $this->assertSame(3, $entity->getCreatedBy());
        $this->assertSame(4, $entity->getUpdatedBy());
    }

    public function test_from_update_dto_preserves_id_and_maps_fields(): void
    {
        $input = new UpdateContractorInputDto(
            id: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            name: 'Updated',
            phone: '1155556666',
            email: 'up@test.local',
            customerId: 'eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee',
            communicationCare: null,
        );

        $entity = ContractorEntityMapper::fromUpdateDto($input);

        $this->assertSame('dddddddd-dddd-dddd-dddd-dddddddddddd', $entity->getId());
        $this->assertSame('Updated', $entity->getName());
        $this->assertSame('1155556666', $entity->getPhone());
        $this->assertSame('up@test.local', $entity->getEmail());
        $this->assertSame('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee', $entity->getCustomerId());
    }
}
