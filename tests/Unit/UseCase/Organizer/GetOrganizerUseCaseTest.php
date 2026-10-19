<?php

declare(strict_types=1);

namespace Tests\Unit\UseCase\Organizer;

use App\Domain\Organizer\Entities\Organizer;
use App\Domain\Organizer\Repositories\OrganizerRepositoryInterface;
use App\UseCase\Organizer\GetOrganizerUseCase;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class GetOrganizerUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_when_input_empty(): void
    {
        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $useCase = new GetOrganizerUseCase($repository);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute('');
    }

    public function test_returns_null_when_not_found(): void
    {
        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee')->andReturn(null);

        $useCase = new GetOrganizerUseCase($repository);
        $this->assertNull($useCase->execute('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee'));
    }

    public function test_returns_output_when_found(): void
    {
        $entity = new Organizer(
            id: 'ffffffff-ffff-ffff-ffff-ffffffffffff',
            customerId: '11111111-1111-1111-1111-111111111111',
            name: 'Encontrado',
            phone: '1188889999',
            email: 'f@test.local',
            communicationCare: null,
            observations: 'See me',
            typeContact: 'Organizer',
            customerName: 'Empresa X',
            createdBy: null,
            createdAt: null,
            updatedBy: null,
            updatedAt: null,
        );

        $repository = Mockery::mock(OrganizerRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->andReturn($entity);

        $useCase = new GetOrganizerUseCase($repository);
        $output = $useCase->execute('ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertNotNull($output);
        $this->assertSame('Encontrado', $output->name);
        $this->assertSame('See me', $output->observations);
        $this->assertSame('Empresa X', $output->customerName);
    }
}
