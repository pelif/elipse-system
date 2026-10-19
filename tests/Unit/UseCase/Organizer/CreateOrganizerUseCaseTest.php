<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Organizer;

use App\Domain\Organizer\Entities\Organizer;
use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\CreateOrganizerUseCase;
use App\UseCase\Organizer\DTO\CreateOrganizerInputDto;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class CreateOrganizerUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_is_invalid(): void
    {
        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $useCase = new CreateOrganizerUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_creates_organizer(): void
    {
        $input = new CreateOrganizerInputDto(
            name: 'Novo Organizador',
            phone: '1188887777',
            email: 'novo@test.local',
            customerId: 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
            communicationCare: 'Care',
            createdBy: 5,
        );

        $createdEntity = new Organizer(
            id: 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            customerId: $input->customerId,
            name: $input->name,
            phone: $input->phone,
            email: $input->email,
            communicationCare: $input->communicationCare,
            observations: null,
            typeContact: 'Organizer',
            customerName: 'Client Display',
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($entity) use ($input): bool {
                return $entity instanceof Organizer
                    && $entity->getId() === null
                    && $entity->getName() === $input->name
                    && $entity->getEmail() === $input->email
                    && $entity->getCustomerId() === $input->customerId
                    && $entity->getObservations() === null;
            }))
            ->andReturn($createdEntity);

        $useCase = new CreateOrganizerUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $output->id);
        $this->assertSame('Novo Organizador', $output->name);
        $this->assertSame('Client Display', $output->customerName);
        $this->assertSame('Care', $output->communicationCare);
        $this->assertNull($output->observations);
    }
}
