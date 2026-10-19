<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Organizer;

use App\Domain\Organizer\Entities\Organizer;
use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\DTO\UpdateOrganizerInputDto;
use App\UseCase\Organizer\UpdateOrganizerUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class UpdateOrganizerUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_invalid(): void
    {
        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $useCase = new UpdateOrganizerUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(null);
    }

    public function test_returns_null_when_update_fails(): void
    {
        $input = new UpdateOrganizerInputDto(
            id: 'cccccccc-cccc-cccc-cccc-cccccccccccc',
            name: 'Novo',
            phone: '1177776666',
            email: 'e@test.local',
            customerId: '22222222-2222-2222-2222-222222222222',
        );

        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn(null);

        $useCase = new UpdateOrganizerUseCase($repository);
        $this->assertNull($useCase->execute($input));
    }

    public function test_returns_output_when_updated(): void
    {
        $input = new UpdateOrganizerInputDto(
            id: 'dddddddd-dddd-dddd-dddd-dddddddddddd',
            name: 'Atualizado',
            phone: '1144445555',
            email: 'e@test.local',
            customerId: '33333333-3333-3333-3333-333333333333',
            communicationCare: 'Up care',
        );

        $updated = new Organizer(
            id: $input->id,
            customerId: $input->customerId,
            name: $input->name,
            phone: $input->phone,
            email: $input->email,
            communicationCare: $input->communicationCare,
            observations: null,
            typeContact: 'Organizer',
            customerName: 'Cliente Atual',
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $repository->shouldReceive('update')->once()->andReturn($updated);

        $useCase = new UpdateOrganizerUseCase($repository);
        $output = $useCase->execute($input);

        $this->assertNotNull($output);
        $this->assertSame('Atualizado', $output->name);
        $this->assertSame('Up care', $output->communicationCare);
        $this->assertNull($output->observations);
        $this->assertSame('Cliente Atual', $output->customerName);
    }
}
