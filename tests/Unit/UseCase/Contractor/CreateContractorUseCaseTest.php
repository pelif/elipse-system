<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Contractor;

use App\Domain\Contractor\Entities\Contractor;
use App\Domain\Contractor\Repositories\ContractorRepositoryInterface;
use App\UseCase\Contractor\CreateContractorUseCase;
use App\UseCase\Contractor\DTO\CreateContractorInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateContractorUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(ContractorRepositoryInterface::class);
        $useCase = new CreateContractorUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_creates_contractor(): void
    {
        $input = new CreateContractorInputDto(
            name: 'Novo Contratante',
            phone: '1188887777',
            email: 'novo@test.local',
            customerId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            communicationCare: 'Care',
            createdBy: 5,
        );

        $createdEntity = new Contractor(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            customerId: $input->customerId,
            name: $input->name,
            phone: $input->phone,
            email: $input->email,
            communicationCare: $input->communicationCare,
            typeContact: 'Contractor',
            customerName: 'Client Display',
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $repository = Mockery::mock(ContractorRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input): bool {
                return $entity instanceof Contractor
                    && $entity->getId() === null
                    && $entity->getName() === $input->name
                    && $entity->getEmail() === $input->email
                    && $entity->getCustomerId() === $input->customerId;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateContractorUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $output->id);
        $this->assertSame('Novo Contratante', $output->name);
        $this->assertSame('Client Display', $output->customerName);
        $this->assertSame('Care', $output->communicationCare);
    }
}
